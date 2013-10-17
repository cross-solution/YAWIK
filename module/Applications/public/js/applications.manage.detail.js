
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
	
	$(function() {
		$('#state-actions button').click(changeStatus);
	});
	
})(jQuery);