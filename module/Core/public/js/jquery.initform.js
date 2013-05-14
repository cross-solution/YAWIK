

(function($) {
	
	$.fn.initform = function( ) {
		
		if (!$.fn.inlineLabel) { throw new Exception('initform requires inlineLabel'); }
		
		return this.each(function() {
			var parent = $(this);
			parent.find(':text, [type="email"], [type="date"], textarea').inlineLabel();
			parent.find("[title]").tooltip();
		    parent.find("input[type=\'submit\'], input[type='checkbox'], button").button();
		    parent.find(".form-element-daterange").daterange();
		    $.datepicker.setDefaults($.datepicker.regional[lang]);
		    parent.find('[type="date"]').datepicker();
		});
	};
	
	
})(jQuery);