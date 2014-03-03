(function($){

	//Validate Forms
	function SearchBlockManager(el, options) {

		//Defaults:
		this.defaults = {
			include_path: path_include,
			root_path: '',
			autocomplete_path: '',
			manage_path: '',
			create_path: '',
			save_path: '',
			search_path: '',				// Path to search over
			parent_id: null,
			custom_item_selector: false		// If the user is going to implement a custom dialog / new page when an item is selected
		};

		//Extending options:
		this.opts = $.extend({}, this.defaults, options);

		//Privates:
		this.$el = $(el);

		this.manager_object = null;
	}

	// Separate functionality from object creation
	SearchBlockManager.prototype = {

		init: function() {
			var _this = this;

			_this.opts.root_path = _this.$el.data('root_path');
			_this.opts.autocomplete_path = _this.opts.root_path + "/data/autocomplete/";
			_this.opts.search_path = _this.opts.root_path + "/data/search/";
			_this.opts.save_path = _this.opts.root_path + "/data/save/";
			_this.opts.manage_path = _this.opts.root_path + "/welcome/manage/";
			_this.opts.create_path = _this.opts.root_path + "/welcome/create/";
			_this.opts.custom_item_selector = _this.$el.data('custom_item_selector');
			_this.opts.parent_id = _this.$el.data('parent_id');


			var obj_name = _this.$el.data('search_manager');

			// If there is a javascript helper object
			if (obj_name != '' ){
				// Eval the helper into this object
				_this.manager_object = eval(obj_name);
				// Initialise the helper
				if (typeof _this.manager_object.init !== 'undefined')
					_this.manager_object.init();
			}
			else {
				_this.manager_object = null;
			}

			_this.init_events();
		},

		init_events: function() {
			var _this = this;

			// Initiate the search button
			$('.btnSearch', _this.$el).click(function() {
				_this.search();
				return false;
			});

			$('.btnAddItem', _this.$el).click(function() {
				_this.show_item(null, true);
			});
			
			/*	Generic Excel Button for Search form set	*/
			$('.btnExcel', _this.$el).click(function() {

				if (!_this.presearch_test()) {
					return;										/*	If preprocessor returns false then exit the search call */
				}
				
				SystemManager.blockUI('Running Excel Report...');

				// Change so we export to excel
				$('.ExcelExport', _this.$el).val(1);

				// If there's no wrapper form around the search
				if (!$('.frmSearch').parent().hasClass('FormWrapper')) {
					// Add a wrapper around the search div
					$('.frmSearch').wrap('<form target="_blank" action="' + _this.opts.include_path + _this.opts.search_path + '" method="post" class="FormWrapper" />');
				}
				// Submit the form
				$('.frmSearch').parent().submit();
				// Change so we don't export when doing a 'normal' search
				$('.ExcelExport', _this.$el).val(0);

				SystemManager.unblockUI();
			});

			//$t.on('keyup', 'form', function(event) {
			_this.$el.on('keypress', 'input', function(event){
				if (event.keyCode == 13) {
					event.preventDefault();
					$('.btnSearch', _this.$el).trigger('click');
					return false;
				}
				return true;
			});

			_this.$el.on('click', '.itemSelector', function() {

				if (!_this.opts.custom_item_selector) {
					_this.show_item($(this));
					return false;
				}

			});
		},

		presearch_test: function () {
			var _this = this;

			// If there is a presearch_text function on the management object
			if (_this.manager_object !== null && typeof _this.manager_object.presearch_test !== 'undefined')
				return _this.manager_object.presearch_test(_this.$el);	// Return the result of the presearch test

			/*
			 * 	Calls the common named pre processor if it exists allowing customisable checkes prior to submission
			 *	Same as Standard search button 
			 */
			if (typeof preProcess === 'function')
				 return preProcess() ;										/*	If preprocessor returns false then exist the search call */

			return true;
		},

				
		show_item: function(element, new_item) {
			var _this = this;

			window.location = _this.opts.include_path + (new_item == true ? _this.opts.create_path + _this.opts.parent_id : _this.opts.manage_path + $(element).parents('tr').data('id'));
			return;
		},

		search: function() {
			var _this = this;
			if (!_this.presearch_test()) {
				return;										/*	If preprocessor returns false then exit the search call */
			}

			SystemManager.blockUI('Searching...');

			$.post(_this.opts.include_path + _this.opts.search_path, $('.frmSearch input,.frmSearch textarea,.frmSearch select', _this.$el).serialize(), function(data) {
				$('.search_results', _this.$el).html(data);

				// If there is a post_search function
				if ( _this.manager_object != null && typeof _this.manager_object.post_search !== 'undefined' ) {
					// Run it
					_this.manager_object.post_search();
				}

				SystemManager.unblockUI();
			})
			.error(function() {
				SystemManager.unblockUI();
			});
		}
	};

	// The actual plugin
	$.fn.searchBlockManager = function(param1, param2) {
		if(this.length) {
			this.each(function() {
				// If we have already been added to the element
				if ($(this).hasClass('hasSearchBlockManager')) {
					var SearchBlockManagerClass = $(this).data('searchBlockManager');
					SearchBlockManagerClass[param1](param2);
				}
				else {
					// Create a new validator and add it to the element
					var rev = new SearchBlockManager(this, param1);
					rev.init();
					$(this).data('searchBlockManager', rev);
					$(this).addClass('hasSearchBlockManager');
				}
			});
		}
	};
})(jQuery);