/**
 * Created by nas on 25/08/15.
 */
define(['jquery', 'system_manager', 'manager_loader', 'bloodhound', 'typeahead' ],function( $, SystemManager, manager_loader ,Bloodhound ) {

    var SearchManager = {

        properties: {
	        initialised: false,
            save_path: "data/save/",
            create_path: "welcome/create/"
        },

        init: function() {

            var _this = this;
            var _prop = _this.properties;

	        if ( _prop.initialised )
	            return;
	        _prop.initialised = true;

            _this._init_typeahead();
            _this._init_events();
            _this._init_buttons();
            _this._init_selection();
	        _this._init_pagination();
            _this._init_breadcrumbs();

            // Event to capture navigating backwards
            $(window).on("popstate", function(e) {
                console.log( 'Popstate' );

                if (e.originalEvent.state !== null) {
                    var $breadcrumbs = $( '#breadcrumb_list li' );
                    if ( $breadcrumbs.length == 1 ) {
                        console.log( 'First page in list' );
                        return;
                    }

                    console.log( 'Triggering click  ' );
                    var $last_breadcrumb = $breadcrumbs.last();
                    $last_breadcrumb.prev().find('a' ).trigger( 'click' );
                }
            });
        },

        _init_typeahead: function() {

            var bmaUsers = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: path_include + 'search/globalsearch/users/index/%QUERY',
                    wildcard: '%QUERY'
                }
            });

            var bmaModels = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: path_include + 'search/globalsearch/models/index/%QUERY',
                    wildcard: '%QUERY'
                }
            });

            var bmaProjects = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: path_include + 'search/globalsearch/projects/index/%QUERY',
                    wildcard: '%QUERY'
                }
            });

            $('#main-navigation .typeahead' ).typeahead( {
		            hint: true,
		            highlight: true,
		            minLength: 1,
	            },
	            {
		            limit: 10,
		            name: 'users',
		            display: 'label',
		            source: bmaUsers,
		            templates: {
			            header: '<h4 class="user-list">Users</h4>'
		            },
	            },
	            {
		            limit: 10,
		            name: 'projects',
		            display: 'label',
		            source: bmaProjects,
		            templates: {
			            header: '<h4 class="user-list">Projects</h4>'
		            },
	            },
	            {
		            limit: 10,
		            name: 'models',
		            display: 'label',
		            source: bmaModels,
		            templates: {
			            header: '<h4 class="user-list">Models</h4>'
		            },
	            } );

            // Event when the user has chosen from the auto-complete
	        $( '#main-navigation').on( 'typeahead:select', '.typeahead', function( ev, suggestion ) {
                window.location = path_include + suggestion.type + '/welcome/manage/' + suggestion.id;
            });
        },

        _init_events: function() {

            var _this = this;

            $( '.page_content').on( 'change', 'input,select,textarea', function() {
                var settings = SystemManager.get_settings( $( this ) );

                settings.settings_element.find( '.navbar').css( 'opacity', 1 );
            });

            $( '.page_content' ).on( 'keypress', '.search-panel', function( e ) {
                if ( e.keyCode == 13 ) {
	                _this._run_search( $( this ), 1, '' );
                }
            });


			/**
			 * Event when the user wants to see a model's card
			 */
			$( '.page_content' ).on( 'click', '.model-selector', function(event){
				var model_id = $( this ).data( 'model-id' );

				var settings = SystemManager.get_settings( $( this ) );
				var search_path = path_include + settings.settings_element.data( 'root_path' );

				_this._load_next_page( path_include + "model/welcome/submanage/" + model_id, settings, search_path + "model/welcome/submanage/" + model_id );
			});

            /**
             * Event when the user wants to see a client's record
             */
            $( '.page_content' ).on( 'click', '.client-selector', function(event){
                var client_id = $( this ).data( 'client-id' );

                var settings = SystemManager.get_settings( $( this ) );
                var search_path = path_include + settings.settings_element.data( 'root_path' );

                _this._load_next_page( path_include + "client/welcome/submanage/" + client_id, settings, search_path + "client/welcome/submanage/" + client_id );
            });
        },

        /**
         * Initialise the core button functionality
         * @private
         */
        _init_buttons: function() {

            var _this = this;
            var _prop = _this.properties;

            // When a search is requested
            $( '.page_content' ).on( 'click', '.btn-search', function( e ) {
	            _this._run_search( $( this ), 1, '' );

                return false;
            });

            // When a new object is requested
            $( '.page_content' ).on( 'click', '.btn-add', function() {

                var settings = SystemManager.get_settings( $( this ) );
                var search_path = path_include + settings.settings_element.data( 'root_path' );

                var $next_page = settings.settings_element.find( '.next_page' );
                var $this_page = settings.settings_element.find( '.this_page' );

                $next_page.empty();

                var $parent_form = $( this ).parents( '.form-search' ).first();
                var parent_id = $parent_form.find( '.ParentId' ).val();
                var parent_type = $parent_form.find( '.ParentType' ).val();
                _this._load_next_page( search_path + _prop.create_path + parent_id + "/" + parent_type, settings );
            })

            $( '.page_content' ).on( 'click', '.btn-save', function() {

                var settings = SystemManager.get_settings( $( this ) );
                var search_path = path_include + settings.settings_element.data( 'root_path' );

                var $this_page = settings.settings_element;
                var $form = settings.settings_element.find( '.form-manage' );

                $.post( search_path + _prop.save_path, $form.serialize(), function( json ) {

                    // If the save wasn't successful
                    if ( json.status !== 'OK' ) {
                        SystemManager.warn_ui( json.notes );
                    }
                    else {
                        SystemManager.inform_ui( 'Saved successfully' );
                        settings.settings_element.find( '.navbar').css( 'opacity', 0.4 );

                        if ( json.is_new ) {

                            $.get( search_path + "welcome/submanage/" + json.id, function( html ) {
                                $this_page.parent().html( html );
                                _this._update_breadcrumbs();
                            });
                        }
                    }
                }, 'json' );
            });

            $( '.page_content' ).on( 'click', '.btn-cancel', function() {

                var settings = SystemManager.get_settings( $( this ) );

                var $this_page = settings.settings_element;
                $this_page.parent().parent().find( '.this_page' ).show();
                $this_page.remove();

                _this._update_breadcrumbs();
            });

        },

        /**
         * Initialise the item selection functionality
         * @private
         */
        _init_selection: function() {

            var _this = this;

            // When a search has happened, and a result is selected
            $( '.page_content' ).on( 'click', '.selection_rows .selection_item,.selection_rows td:not(.no-selector)', function() {

                var settings = SystemManager.get_settings( $( this ) );
                var search_path = path_include + settings.settings_element.data( 'root_path' );
	            var $selection_row = $( this ).parents( '.selection_row' );

                // If there is a search manager
	            if ( settings.manager_object !== '' ) {
		            // Get the relevant object
		            var page_search_manager = require( settings.manager_object );
		            // If there is a pre-select function
		            if( typeof page_search_manager.pre_select == 'function' ) {
			            // Check we are allowed to continue
			            if ( !page_search_manager.pre_select( $selection_row ) )
			                // Continuing is blocked - return
			                return;
		            }
	            }

                _this._load_next_page( search_path + "welcome/submanage/" + $selection_row.data( 'id' ), settings, search_path + "welcome/manage/" + $selection_row.data( 'id' ) );
            });

        },

        _init_pagination: function() {

	        var _this = this;

	        $( '.page_content' ).on( 'click', 'ul.pagination a', function() {

		        var page_number = $( this ).data( 'page_number' );

		        _this._run_search( $( this ), page_number, null );
	        });

	        $( '.page_content' ).on( 'click', 'thead.search-results-header th[data-sort]', function() {

		        var sort_order = $( this ).data( 'sort' );
		        var sort_direction = $( this ).data( 'sort-dir' );

		        _this._run_search( $( this ), null, sort_order, sort_direction );
	        });
        },

        /**
         * Initialise the breadcrumb functions
         * @private
         */
        _init_breadcrumbs: function() {

            var _this = this;

            // When a breadcrumb is clicked
            $( '#breadcrumb_list' ).on( 'click', 'a', function() {
                element = $( this ).parent().data( 'element' );
                $( element ).find( '.next_page' ).empty();
                $( element ).find( '.this_page' ).show();

                _this._update_breadcrumbs();
	            return false;
            });

        },

        /**
         * Function to update the breadcrumb list
         * @private
         */
        _update_breadcrumbs: function() {

            $( '#breadcrumb_list' ).empty();
            $( '.settings-element:not(.nobreadcrumb)' ).each( function( index, element ) {

                var breadcrumb = $(element).data( 'breadcrumb' );
                var $crumb = $( "<li><a href='#'>" + breadcrumb + "</a></li>" );
                $crumb.data( 'element', element );
                $( '#breadcrumb_list' ).append( $crumb );
            });

        },

        _load_next_page: function( url, settings, history_url ) {
            var _this = this;

            if ( typeof history_url == 'undefined' ) {
                history_url = url;
            }
            var $this_page = settings.settings_element.find( '.this_page' );
	        var $next_page = settings.settings_element.find( '.next_page' ).last();

            if ( settings.settings_element.hasClass( 'nobreadcrumb' ) ) {
                $this_page = $this_page.parents( '.this_page:first' );
                $next_page = $this_page.next();
            }

            //history.pushState({id: 'SOME ID'}, '', history_url);
            $.get( url, function( html ) {

                $this_page.hide();
                $next_page.html( html );
                // Load any required modules for this section
                manager_loader.load( $next_page );
                SystemManager.init_styles( $next_page );

                _this._update_breadcrumbs();

	            $( '.btn-search', $next_page ).trigger( 'click' );
            });

        },

	    _run_search: function( $element, page_number, sort_order, sort_direction ) {
		    var _this = this;

		    var settings = SystemManager.get_settings( $element );
		    var search_path = path_include + settings.settings_element.data( 'root_path' );

		    var $search_form = settings.settings_element.find( '.form-search'),
			    $search_results = settings.settings_element.find( '.search-results' ),
			    $next_page = settings.settings_element.find( '.next_page' );

		    if ( page_number !== null ) {
			    $search_form.find( '.PageNumber' ).val( page_number );
		    }
		    if ( sort_order !== null ) {
			    $search_form.find( '.SortOrder' ).val( sort_order );
			    $search_form.find( '.SortDirection' ).val( sort_direction );
		    }

		    $next_page.empty();
		    _this._update_breadcrumbs();

		    SystemManager.blockUI( "Searching..." );
		    $.post( search_path + "data/search" , $search_form.find( 'input, select, textarea' ).serialize(), function( html ){
			    $search_results.html( html );
			    SystemManager.unblockUI();
		    });

	    }
    };

    return SearchManager;
});