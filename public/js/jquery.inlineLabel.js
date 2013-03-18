

(function($) {
	
	var inlineLabelListener = function(e) {
		var target = $(e.target);
		var currentValue = target.val();
		
		if ('focus' == e.type && e.data.text == currentValue) {
			target.css('color', "").val("");
		} else if (
			'blur' == e.type 
			&& ("" == currentValue || e.data.text == currentValue)
		) {
			target.css('color', e.data.color).val(e.data.text);
		}
	};
	
	$.fn.inlineLabel = function( options ) {
		
		var opt = $.extend({labelColor: "#888"}, options);
		
		return this.each(function() {
			
			var label = $('label[for="' + $(this).attr('id') + '"]');
			
			if (label.length) {
				var text = label.hide().text();
				$(this).on(
					'focus.inlineLabel blur.inlineLabel', 
					{'text': text, color: opt.labelColor},
					inlineLabelListener
				);
				$(this).trigger('blur.inlineLabel');
			}
		});
		
	};
	
	
})(jQuery);