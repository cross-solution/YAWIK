
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
            $button.addClass('disabled').prop('disabled', true);
		} else {
			$button.find('.processing').addClass('yk-hidden');
			$button.find('.default').removeClass('yk-hidden');
			$button.data('state', 'default');
            $button.removeClass('disabled').prop('disabled', false);
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
	
	$(function() {
        $('body').on('click.spinnerbutton.data-api', '[data-provide="spinnerbutton"]', function(e) {
             var $button = $(this);
            if ($button.find('.processing').length) {
                if (!$button.data('state')) {
                    $button.spinnerbutton();
                }
                $button.spinnerbutton('toggle');
            }
        });
        $('button').spinnerbutton();
    });
	
})(jQuery);