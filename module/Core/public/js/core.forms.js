
;(function($) {
	
	displayMessages = function(messages, prefix)
	{
		$.each(messages, function(element, msgs) {
			var $errorsDiv = $("#" + prefix + element + "-errors");
			if ($errorsDiv.length) {
				var errorList = '';
				$.each(msgs, function(key, msg) {
					errorList += "<li>" + msg + "</li>";
				});
				$errorsDiv.html('<ul>' + errorList + '</ul>');
				$errorsDiv.parent().addClass('input-error');
				
			} else if ("object" == $.type(msgs)){
				displayMessages(msgs, element + '-');
			}
		});
	};
	
	formSubmit = function(event)
	{
		processResponse = function(data)
		{
			if (data.redirect) {
				location.href = data.redirect;
				return;
			}
			var $alert = $('#' + $form.attr('name') + '-response');
			$alert.addClass('alert-' + data.status)
			      .html(data.text)
			      .removeClass('hide');
			if (data.messages) {
				data.messages.test = { huch : { kuchen : 'tütü'}};
				displayMessages(data.messages, '');
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