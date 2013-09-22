(function($){

	//Validate Forms
	function SearchBlockManager(el, options) {

		//Defaults:
		this.defaults = {
		};

		//Extending options:
		this.opts = $.extend({}, this.defaults, options);

		//Privates:
		this.$el = $(el);

		this.search_path = '';
		this.detail_path = '';
		this.save_path = '';
		this.manage_path = '';
		this.manager_object = null;
		this.custom_item_selector = false;	// If the user is going to implement a custom dialog / new page when an item is selected
		this.use_datatable = true;
	}

	// Separate functionality from object creation
	SearchBlockManager.prototype = {

		init: function() {
			var _this = this;

			_this.search_path = _this.$el.data('search_path');
			_this.detail_path = _this.$el.data('detail_path');
			_this.save_path = _this.$el.data('save_path');
			_this.manage_path = _this.$el.data('manage_path');
			_this.custom_item_selector = _this.$el.data('custom_item_selector');
			_this.use_datatable = _this.$el.data('use_datatable') != false;

			var search_results_width = "1500px";
			if (_this.$el.data('search_results_width')){
				search_results_width = _this.$el.data('search_results_width');

			}
			_this.datatable_options = {
				"sScrollY": "500px",
				"sScrollX": "100%",
				"sScrollXInner": search_results_width,
				"bScrollCollapse": true,
				"bPaginate": false,
				"bFilter": false
			};

			var obj_name = _this.$el.data('manager');

			if (obj_name != '' ){
				_this.manager_object = window[obj_name]()
				if (typeof _this.manager_object.init != 'undefined') {
					_this.manager_object.init($('.dlgDetails', _this.$el));

					// If there's a manager object with a table sorting options function
					if (typeof _this.manager_object.table_sorting_options != 'undefined') {
						// Extend the default sort options with the result of the function call
						$.extend(_this.datatable_options, _this.manager_object.table_sorting_options());
					}
				}
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
				if (!_this.presearch_test()) {
					return;										/*	If preprocessor returns false then exit the search call */
				}
				
				$.blockUI({ message: '<h1><img src="' + path_root + 'resources/images/ajax-loader-small.gif" /> &nbsp; Searching...</h1>' });

				var search_path = _this.search_path;
				
				if ( typeof ($(this).data('search_path')) != "undefined" && $(this).data('search_path') != ""){
					search_path = 	path_include + $(this).data('search_path');
				}
				

				$.post(search_path, $('.frmSearch input,.frmSearch textarea,.frmSearch select', _this.$el).serialize(), function(data) {

					$('.search_results', _this.$el).html(data);

					if (data.indexOf("No results") === -1) {

						$.unblockUI();												/*	temporary fix to solve hangs in this code block */
						// If we are to use datatable
						if (_this.use_datatable) {
							// Use datatables
							var oTable = $('table.ResultsTable', _this.$el).dataTable(_this.datatable_options);
							new FixedColumns( oTable, {
								"iLeftColumns": 1,
								"iLeftWidth": 200
							} );
						}
						else {
							// Otherwise style the result set
							$('table.ResultsTable').addClass('dataTable');
							$('table.ResultsTable tbody tr:even').addClass('even');
							$('table.ResultsTable tbody tr:odd').addClass('odd');
						}
					}
					$.unblockUI();
				})
				.error(function() {
					$.unblockUI();
				});
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
				
				$.blockUI({ message: '<h1><img src="' + path_root + 'resources/images/ajax-loader-small.gif" /> &nbsp; Running Excel Report...</h1>' });

				// Change so we export to excel
				$('#ExcelExport', _this.$el).val(1);

				// If there's now wrapper form around the search
				if (!$('.frmSearch').parent().hasClass('FormWrapper')) {
					// Work out what the search path should be
					var search_path = _this.search_path;

					// Add a wrapper around the search div
					$('.frmSearch').wrap('<form target="_blank" action="' + search_path + '" method="post" class="FormWrapper" />');
				}
				// Submit the form
				$('.frmSearch').parent().submit();
				// Change so we don't export when doing a 'normal' search
				$('#ExcelExport', _this.$el).val(0);

				$.unblockUI();
			});

			// Initiate the dialog
			_this.$dialog = $('.dlgDetails', _this.$el).dialog({
				autoOpen: false,
				modal: true,
				title: 'Dialog',
				minWidth: 800,
				width: 800,
				minHeight: 400,
				maxHeight: 400,
				buttons: {
					Cancel: function() {
						$(this).dialog('close');
					},
					OK: function() {
						$.blockUI({ message: '<h1><img src="' + path_root + 'resources/images/ajax-loader-small.gif" /> &nbsp; Saving...</h1>' });

						$d = $(this);
						if (typeof _this.manager_object.pre_save != 'undefined') {
							_this.manager_object.pre_save();
						}

						$.post(_this.save_path, $('.frmDialog input,.frmDialog textarea,.frmDialog select', $d).serialize(), function(data) {
							// Close the dialog
							$d.dialog('close');
							// Trigger the search to run again
							$('.btnSearch', _this.$el).trigger('click');
						}, 'json')
						.error(function() {
							$.unblockUI();
						});
					}
				}
			});

			//$t.on('keyup', 'form', function(event) {
			$('form', _this.$el).bind("keypress", function(event){
				if (event.keyCode == 13) {
					event.preventDefault();
					$('.btnSearch', _this.$el).trigger('click');
					return false;
				}
				return true;
			});

			_this.$el.on('click', '.itemSelector', function() {

				if (!_this.custom_item_selector) {
					_this.show_item($(this));
					return false;
				}

			});
		},

		presearch_test: function () {
			var _this = this;

			// If there is a presearch_text function on the management object
			if (_this.manager_object !== null && typeof _this.manager_object.presearch_test !== 'undefined')
				return _this.manager_object.presearch_test();	// Return the result of the presearch test

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

			var manage_path = _this.manage_path;
			
			/*if (element.data('manage_path')){
				manage_path = path_include + 	element.data('manage_path');
			}*/
			

			if (typeof manage_path != 'undefined' && manage_path != '') {
				window.location = manage_path + (new_item == true ? '' : $(element).parents('tr').data('id'));
				return;
			}

			if (new_item == true) {
				_this.manager_object.show_details({}, function() {
					// Display the dialog
					_this.$dialog.dialog('open');
				});
			}
			else {
				// Find the id of the object
				var id = $(element).parents('tr').data('id');

				// Block the UI to show we are doing something
				$.blockUI({ message: '<h1><img src="' + path_root + 'resources/images/ajax-loader-small.gif" /> &nbsp; Searching...</h1>' });

				// Actually search for the object's data
				$.post(_this.detail_path + id, function(data) {
					_this.manager_object.show_details(data, function() {
						// Display the dialog
						_this.$dialog.dialog('open');
					});

					// Unblock the UI
					$.unblockUI();
				}, 'json')
				.error(function() {
					// An error has occurred whilst searching
					// Unblock the UI
					$.unblockUI();
				});
			}

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