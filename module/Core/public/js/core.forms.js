
(function($) {
	
	formSubmit = function(event)
	{
		processResponse = function(data)
		{
			if (true === data.ok) {
				var $alert = $('#' + $form.attr('name') + '-response');
				$alert.addClass('alert-' + data.status)
				      .html(data.text)
				      .removeClass('hide');
			}
		};
		
		var $form = $(event.target);
		var data  = $form.serialize();
		
		$.post($form.attr('action'), data, processResponse, 'json');
		return false;
	}
	
	
	$.fn.forms = function()
	{
		return this.each(function() {
			$(this).on('submit.forms', formSubmit);
		});
	};
	
	$(function() {
		$('form').forms();
	});
	
})(jQuery);