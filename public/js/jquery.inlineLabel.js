

(function($) 
{
	var handlers = 
	{
		onFocusOrBlur: function(e) 
		{
			var $target = $(e.target);
			var currentValue = $target.val();
			
			if ('focus' == e.type && e.data.text == currentValue) {
				$target.css('color', "").val("");
			} else if (
				'blur' == e.type 
				&& ("" == currentValue || e.data.text == currentValue)
			) {
				$target.css('color', e.data.color).val(e.data.text);
			}
		}
	};
	
	
	var methods = 
	{
		main: function(options)
		{
			var opt = $.extend({labelColor: "#888"}, options);
			
			return this.each(function() {
				var $this = $(this);
				var $label = $('label[for="' + $this.attr('id') + '"]');
				
				if ($label.length) {
					var text = $label.hide().text();
					
					$this.on(
						'focus.inlineLabel blur.inlineLabel', 
						{'text': text, color: opt.labelColor},
						handlers.onFocusOrBlur
					);
					$this.data('inlineLabel', text); // needed for clear
					$this.trigger('blur.inlineLabel');
				}
			});
		},
		
		clear: function()
		{
			return this.each(function() {
				var $this = $(this);
				if ((text = $this.data('inlineLabel')) && text == $this.val()) {
					$this.val('');
				}
			});
		}
	};
	
	$.fn.inlineLabel = function( method ) {
		
		if ( methods[method] ) {
		      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
		      return methods.main.apply( this, arguments );
		} else {
			throw new Error('Method ' + method + ' does not exists on jquery.inlineLabel');
		}    
	};
	
})(jQuery);