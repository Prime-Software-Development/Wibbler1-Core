/**
 * Created by nas on 24/08/15.
 */
define( [ 'jquery', 'dropdown_manager', 'require', 'validator', 'sweet_alert', 'growl', 'block-ui', 'datepicker' ], function ( $, DropDownManager ) {

	var SystemManager = {
		init: function () {
			var _this = this;

			var datepicker = $.fn.datepicker.noConflict(); // return $.fn.datepicker to previously assigned value
			$.fn.bootstrapDP = datepicker;

			_this.init_events();
			_this.init_styles( 'body' );
			_this.init_timeout();
		},

		init_events: function () {
			var _this = this;
			/** User clicked on document that can be previewed **/
			$( 'body' ).on( 'click', '.ViewBoxItem', function ( event ) {
				event.preventDefault();
				var $box_item = $( this ),
					type = $box_item.data( 'file_type' );
				_this.view_box( this, type );
			} );
			/** Close event when user clicks on close button or on the edge of the ViewBoxWrapper **/
			$( 'body' ).on( 'click', '.ViewBoxWrapper', function ( event ) {
				var $box = $( this );
				if ( $( event.target ).hasClass( 'ViewBoxClose' ) || event.target == this ) {
					var $sub_containers = $box.find( '.ViewBox' );
					$sub_containers.hide();
					$sub_containers.find( 'img' ).hide().attr( 'src', '' );
					$sub_containers.find( 'iframe' ).attr( 'src', '' );
					$box.hide();
				}
			} );

			/** Pretty box image on load event listener **/
			$( '.ViewBoxWrapper .ViewBoxImage img' ).load( _this.image_loaded );
		},

		init_styles: function ( $elem ) {

			$( '.datepicker', $elem ).bootstrapDP( {
				format: "dd-mm-yyyy",
				autoclose: true,
			});

			DropDownManager.init();
		},

		/**
		 * Preview document in a pretty box
		 * @param element
		 * @param type
		 */
		view_box: function ( element, type ) {
			var _this = this;
			var $box = $( '.ViewBoxWrapper' ),
				$image_container, $img, $pdf_container, $iframe;

			// If we are in an iframe get top level document
			if ( window.top !== window.self ) {
				$box = $( '.ViewBoxWrapper', window.top.document );
			}

			if ( $box.length == 0 ) {
				alert( "There was an error, can not preview the file." );
			}
			if ( type == 'png' || type == 'jpg' || type == 'jpeg' || type == 'gif' ) {
				$image_container = $box.find( '.ViewBoxImage' );
				$img = $image_container.find( 'img' );

				$box.show();
				$image_container.show();
				$img.attr( 'src', $( element ).data( 'url' ) );
			}
			if ( type == 'pdf' ) {
				$pdf_container = $box.find( '.ViewBoxPdf' );
				$iframe = $pdf_container.find( 'iframe' );

				$box.show();
				$pdf_container.show();
				$iframe.attr( 'src', $( element ).data( 'url' ) );
			}
			if ( type == 'vid' ) {
				$pdf_container = $box.find( '.ViewBoxVideo' );
				$iframe = $pdf_container.find( 'iframe' );

				$box.show();
				$pdf_container.show();
				$iframe.attr( 'src', 'http://player.vimeo.com/video/' + $( element ).data( 'video_link' ) );
			}
		},

		/** Display ViewBox image once it's loaded (Pretty box)**/
		image_loaded: function ( event ) {
			$( event.target ).fadeIn();
		},

		/**
		 *
		 * @param $element
		 * @returns {{frmSearch: *, result_block: *, manage_object: *}}
		 */
		get_settings: function ( $element ) {

			var _this = this;
			var _prop = _this.properties;

			var $settings_element = $element.parents( '.settings-element' ).first();
			var cache_key = $settings_element.data( 'manager' );
			var manager_object = cache_key;

			var result = {
				settings_element: $settings_element,
				manager_object: manager_object,
				root_path: $settings_element.data( 'root_path' ),
				id: $settings_element.data( 'id' )
			}

			return result;
		},

		_form_has_errors: function ( $el ) {

			$el.validator( 'validate' );
			return $el.find( '.has-error' ).not( ':hidden' ).length > 0;

		},

		_field_has_errors: function ( $el ) {

			$el.parents( 'form' ).validator( 'validate' );
			return $el.parents( '.form-group' ).hasClass( 'has-error' );

		},

		_show_alert: function ( $el, alert_text, level ) {
			if ( typeof level == 'undefined' )
				level = 'warning';
			$alert = $( '<div class="alert alert-' + level + '" role="alert"><span class="AlertText">' + alert_text + '</span></div>' );
			$el.append( $alert );

			$alert.fadeTo( 2000, 500 ).slideUp( 500, function () {
				$alert.alert( 'close' );
			} );
		},

		/**
		 * Warns the user about something
		 * @param message
		 */
		warn_ui: function ( message ) {
			$.bootstrapGrowl( "<div style='font-size:1.2em;font-weight:bold;'>BMA</div>" + message, { type: 'danger' } );
		},

		/**
		 * Informs the user about something
		 * @param message
		 */
		inform_ui: function ( message ) {
			$.bootstrapGrowl( "<div style='font-size:1.2em;font-weight:bold;'>BMA</div>" + message, { type: 'success' } );
		},

		/**
		 * Blocks the user interface showing a simple message
		 * @param message
		 */
		blockUI: function ( message ) {
			$.blockUI( { message: '<h1><i class="fa fa-spinner fa-spin"></i> &nbsp; ' + message + '</h1>' } );
		},

		/**
		 * Unblocks the user interface
		 */
		unblockUI: function () {
			$.unblockUI();
		},

		/**
		 * Initialise the user timeout checking
		 */
		init_timeout: function() {
			var t = this;

			var do_session_check = $( '.page-holder' ).data( 'session-check' );
			if ( do_session_check ) {
				setInterval( function () {
					t.check_session();
				}, 30000 );
			}
		},


		/**
		 * Checks the session to make sure another window isn't keeping everything alive
		 */
		check_session: function() {
			var t = this;
			var p = t.properties;

			$.getJSON(path_include + 'welcome/check_session/', function(data) {
				// If the session has expired at the server end
				if (data.status != 'OK') {
					window.location = path_include;
					return false;
				}
			});
		}

	};

	SystemManager.init();

	return SystemManager;
} );