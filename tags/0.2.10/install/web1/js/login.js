/**
 * Created by nas on 25/08/15.
 */
define(['jquery','system_manager', 'domReady!'],function($, SystemManager ) {

	// Return login module
	return {
		init: function() {
			/**
			 * Event when the new colleague wants to signup
			 */
			$( '#btn_login' ).click( function() {

				if ( SystemManager._form_has_errors( $( '#form_signin' ) ) ) {
					SystemManager._show_alert( $('#panel_login_error'), 'Please complete all of the required fields', 'warning' );
					return;
				}

				// Actually submit the form - all fields are validated
				$( '#form_signin' ).submit();
			});
		}
	};
});
