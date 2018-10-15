/**
 * Created by nas on 06/10/15.
 */
define( [ 'jquery' ], function ( $ ) {

	var DropDownManager = {
		initialised: false,
		cache: {},

		init: function () {

			var _this = this;

			_this.init_dropdowns( $( '#MainContent, #DialogContainer' ) );
		},

		init_dropdowns: function ( $elem ) {

			var _this = this;
			var cache = _this.cache;

			$( 'select[data-dropdown]', $elem ).each( function () {

				var $dropdown = $( this );

				// Check if the drop down is already activated
				if ( $dropdown.data( 'activated' ) ) {
					// It is so stop now
					return;
				}

				// Note the drop down is activated
				$dropdown.data( 'activated', true );

				// Get which element is linked to this one (if any)
				var child_elem = $dropdown.data( 'child' );

				if ( typeof child_elem != 'undefined' ) {
					var children = child_elem.split( "," );

					$.each( children, function ( index, child ) {
						var selector = $.trim( child );
						$dropdown.change( function () {
							_this.show_options( $( '#' + selector ), $dropdown.val() );
						} );
					} );
				}

				var parent_id = $dropdown.data( 'parent-id' );
				_this.show_options( $dropdown, typeof parent_id == 'undefined' ? 'all' : parent_id );

				$dropdown.on( "Dropdown:Reload", function( event ) {
					var parent_id = $dropdown.data( 'parent-id' );
					_this.show_options( $dropdown, typeof parent_id == 'undefined' ? 'all' : parent_id );
				} );
			} );

		},

		show_options: function ( $dropdown, parent_id ) {

			var _this = this;
			var cache = _this.cache;

			// Note whether we should use the cache
			var use_cache = !( $dropdown.data( 'dropdown-cache' ) === false );

			// Get the source for the data
			var source = $dropdown.data( 'dropdown' );
			var source_root = $dropdown.data( 'source_root' );

			// Get which item should be pre-selected
			var selected_id = $dropdown.data( 'selected-id' );

			// Convert the source to a url
			if ( source_root ) {
				var url = path_include + source_root + source;
			} else {
				var url = path_include + 'search/' + source + 's/';
			}

			// Get the name of the parent object (if relevant)
			var parent_name = $dropdown.data( 'parent-name' );

			// Get expected results format (if relevant)
			var format = $dropdown.data( 'results-format' );

			// Get default value if present
			var default_value = $dropdown.is( '[data-default]' ) ? $dropdown.data( 'default' ) : "ALL";

			// Empty the drop down
			$dropdown.empty();

			if ( default_value != false ) {
				$dropdown.append( '<option value="">' + default_value + '</option>' );
			}

			if ( use_cache && typeof cache[ source ] != 'undefined' && typeof cache[ source ][ parent_id ] != 'undefined' ) {
				_this.fill_dropdown( $dropdown, cache[ source ][ parent_id ], selected_id );
			}
			else {
				var get_data = {};
				get_data[ 'format' ] = format;
				$.extend( get_data, $dropdown.data( 'filters' ) || {} );

				// Only filter results by an actual id not string 'all'
				if ( typeof parent_name != 'undefined' && parent_id != 'all' ) {
					get_data[ parent_name ] = parent_id;
				}

				$.getJSON( url, get_data, function ( data ) {
					if ( typeof cache[ source ] == 'undefined' )
						cache[ source ] = [];
					cache[ source ][ parent_id ] = data;

					_this.fill_dropdown( $dropdown, data, selected_id );
				} );
			}
		},

		/**
		 * Fills the drop down with the values
		 * @param $dropdown
		 * @param data
		 * @param selected_id
		 */
		fill_dropdown: function ( $dropdown, data, selected_id ) {
			if ( data === undefined ) return;
			$.each( data, function ( index, value ) {
				var selected_text = '';
				if ( selected_id == value.id ) {
					selected_text = "selected='selected'";
				}
				// Append the next option to the drop down
				var tmp_item = $( '<option value="' + value.id + '" ' + selected_text + '>' + value.name + '</option>' );
				// Add the depot's data to the option
				tmp_item.data( 'data', value );
				// Add the option to the drop down
				$dropdown.append( tmp_item );
			} );

		}
	};

	return DropDownManager;
} );