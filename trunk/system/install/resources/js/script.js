var CMSManager = {
	properties: {
		is_ie6: false
	},

	init: function() {
		var t = this;

		t.init_style();
		t.init_events();

		$('.SearchBlock').searchBlockManager();
	},

	init_style: function() {
		var t = this;
		var p = t.properties;

		// Check if we are on IE 6 or lower
		p.is_ie6 = ( navigator.userAgent.match(/msie/i) && navigator.userAgent.match(/msie 6/) );

		// Only show the buttons if show buttons is true - this is so we can filter out IE6
		if (!p.is_ie6) {
			$('.button').button();
		}

		$('.datepicker').datepicker({dateFormat: 'dd-mm-yy', changeYear: true, yearRange:'1930:+1'});
	},

	init_events: function(){
		var t = this;
		var p = t.properties;
	}
}

$(document).ready(function(){
	CMSManager.init();
});