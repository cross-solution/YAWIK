
;(function($) {
	
	function buttonClicked(event)
	{
		var $button = $(event.currentTarget);
		toggleState($button);
		
	};
	
	function toggleState($button)
	{
		if ('default' == $button.data('state')) {
			$button.find('.processing').removeClass('yk-hidden');
			$button.find('.default').addClass('yk-hidden');
			$button.data('state', 'processing');
		} else {
			$button.find('.processing').addClass('yk-hidden');
			$button.find('.default').removeClass('yk-hidden');
			$button.data('state', 'default');
		}
	}
	
	$.fn.spinnerbutton = function(action, options) 
	{
		if ('toggle' == action) {
			return this.each(function() { toggleState($(this)) });
			
		} else if ('' == action || undefined == action) {
		
			return this.each(function()	{	
				var $button = $(this);
				if ($button.find('.processing')) {
					$button.data('state', 'default');
					$button.click(buttonClicked);
				}
			});
		}
		
		return this;
	};
	
	$(function() { $('button').spinnerbutton(); });
	
})(jQuery);