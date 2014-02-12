
;(function($) {

	var changeStatus = function(event)
	{
		var $target = $(event.target);
		
		if ($target.data('toggle')) {
			$("#mail-box-label").html($target.data('title'));
			$("#mail-box-content").load($target.data('href'));
		} else {
			$.get($target.data('href'))
			 .done(function() {
				 location.reload();
			 })
			 .fail(function(jqXHR, status, error) {
				 console.debug(jqXHR, status, error);
			 });
		}
		return;
		var uri = "/" + lang + "/applications/" 
		        + $target.data("applicationId") + "/"
		        + $target.data("action") + "?format=json";
		
		$.get(uri)
		 .done(function(result) { console.debug(result); })
		 .fail(function(jqXHR, textStatus, errorThrown) {
			 console.debug(jqXHR, textStatus, errorThrown);
		  });
		
		
		
	};
	
	var forwardEmailHandler = function(event)
	{
		var displayResult = function(text, type)
		{
			alert = $('#forward-email-result');
			alert.addClass('alert-' + type);
			alert.html(text);
			alert.slideDown();
			window.setTimeout(function() { alert.removeClass('alert-' + type); alert.slideUp(); }, 3000);
		};
		
		var $formular = $(event.target);
		if ('' == $formular.find('#forward-email-input').val()) {
			return false;
		}
		
		var $alert = $('#forward-email-result');
		
		$.get($formular.attr('action') + '?' + $formular.serialize())
		 .done(function (data) {
			 displayResult(data.text, data.ok ? 'success' : 'error');
		 })
		 .fail(function (jqXHR, textStatus, errorThrown) {
			 displayResult('Unexpected error: ' + jqXHR.status + ' ' + jqXHR.statusText, 'error');
		 });
		return false;
	};
	
	$(function() {
		$('#state-actions button').click(changeStatus);
		$('#forward-email span').popover();
		$('#forward-email-form').submit(forwardEmailHandler);
	});
	
})(jQuery);