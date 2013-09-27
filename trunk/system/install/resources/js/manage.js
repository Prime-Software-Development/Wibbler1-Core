(function($){

	//Validate Forms
	function ManageBlockManager(el, options) {

		//Defaults:
		this.defaults = {
			include_path: path_include,
			root_path: '',
			autocomplete_path: '',
			manage_path: '',
			save_path: '',
			search_path: '',				// Path to search over
			custom_item_selector: false		// If the user is going to implement a custom dialog / new page when an item is selected
		};

		//Extending options:
		this.opts = $.extend({}, this.defaults, options);

		//Privates:
		this.$el = $(el);
	}

	// Separate functionality from object creation
	ManageBlockManager.prototype = {

		init: function() {
			var _this = this;

			_this.opts.root_path = _this.$el.data('root_path');
			_this.opts.autocomplete_path = _this.opts.root_path + "/data/autocomplete/";
			_this.opts.search_path = _this.opts.root_path + "/data/search/";
			_this.opts.save_path = _this.opts.root_path + "/data/save/";
			_this.opts.manage_path = _this.opts.root_path + "/welcome/manage/";
			_this.opts.custom_item_selector = _this.$el.data('custom_item_selector');

			var obj_name = _this.$el.data('manage_manager');

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

			/*********
			  SEARCH BAR
			*********/
			$('#txt_quick_search', _this.$el).autocomplete({
				source: _this.opts.include_path + _this.opts.autocomplete_path,
				select: function( event, ui ) {
					window.location.href = _this.opts.include_path + _this.opts.manage_path + ui.item.id;
				}
			});

			$('#a_show_full_search', _this.$el).click(function(e) {

				$(this).html( ($(this).html() == 'more...' ? 'hide' : 'more...') );

				$('#panel_full_search').slideToggle();

			});

			/*********
			  SAVE
			*********/
			$('#btn_panel_search_save', _this.$el).click(function() {

				SystemManager.blockUI('Saving...');

				$.post(_this.opts.include_path + _this.opts.save_path, $('#form_user').serialize(), function(data) {
					SystemManager.unblockUI();
					SystemManager.informUI('Saved Successfully')

					if (data.new === true) {
						window.location.href = _this.opts.include_path + _this.opts.manage_path + data.id;
					}

				}, 'json')
				.error(function() {
					SystemManager.unblockUI();
				});
			});

			$('#btn_panel_search_discard').click(function(e) {
			   document.location.reload(true);
			});
		}
	};

	// The actual plugin
	$.fn.manageBlockManager = function(param1, param2) {
		if(this.length) {
			this.each(function() {
				// If we have already been added to the element
				if ($(this).hasClass('hasManageBlockManager')) {
					var ManageBlockManagerClass = $(this).data('manageBlockManager');
					ManageBlockManagerClass[param1](param2);
				}
				else {
					// Create a new validator and add it to the element
					var rev = new ManageBlockManager(this, param1);
					rev.init();
					$(this).data('manageBlockManager', rev);
					$(this).addClass('hasManageBlockManager');
				}
			});
		}
	};
})(jQuery);