
;(function ($) {
	
	$(function () 
	{
		setTimeout(
			function() { $('.yk-notifications .alert').slideUp(250); },
			3000
		);
		$('.yk-notifications .alert button').click(function(event) {
			$(event.target).parent().slideUp(250);
			return false;
		});
		
	});

})(jQuery);