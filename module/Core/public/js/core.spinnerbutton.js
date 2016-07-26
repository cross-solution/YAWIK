
;(function($) {
	
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
	
	$.fn.spinnerbutton = function(action)
	{
		if ('toggle' == action) {
			return this.each(function() { toggleState($(this)) });
			
		} else if ('' == action || undefined == action) {
		
			return this.each(function()	{	
				var $button = $(this);
                var $form = $button.closest('form');

				if ($button.find('.processing')) {
					$button.data('state', 'default');
                    $form.on(
                        'yk:forms:start.yk.core.spinnerbutton yk:forms:success.yk.core.spinnerbutton',
                        function() { toggleState($button); }
                    );
				}
			});
		}
		
		return this;
	};
	
	$(function() {
          $('button[data-provide="spinner-button"]').spinnerbutton();
    });
	
})(jQuery);