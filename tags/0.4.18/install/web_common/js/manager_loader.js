/**
 * Created by nas on 24/08/15.
 */
define( [ 'jquery' ], function ( $, SystemManager ) {

	return {
		init: function ( $container ) {
			var _this = this;
			if ( typeof $container === 'undefined' ) {
				$container = $( 'body' );
			}
			_this.load( $container );
		},
		/**
		 * Find all the setting-element block within given container
		 * and load required manager modules
		 * @param $container
		 */
		load: function ( $container ) {
			var _this = this, $elements = $container.find( '.settings-element' );

			$elements.each( function ( index, ele ) {
				var $ele = $( ele ), manager_name = $ele.data( 'manager' );
				if ( manager_name ) {
					// Load the manager module and initialise
					_this._load_manager( manager_name, $ele );
				}
			} );
		},
		/**
		 * Load a manager
		 * @param manager_name - name of the manager to load
		 * @param $element - element the manager belongs to
		 * @private
		 */
		_load_manager: function ( manager_name, $element ) {

			require( [ manager_name ], function ( manager ) {

				// Manager is plain javascript, e.g jQuery plugin
				if ( manager === undefined ) return;
				// Initialise manager once
				if ( !(manager.initialised === true) && typeof manager.init === 'function' ) {
					manager.init( $element );
					// Note that initialised
					// TODO probably init function should be responsible for setting the flag
					manager.initialised = true;
				}
				// Run set up for the specific container that required this manager module
				if ( typeof manager.on_load === 'function' ) {
					manager.on_load( $element )
				}

				$element.data( 'manager-object', manager );
			} );
		}
	};
} );