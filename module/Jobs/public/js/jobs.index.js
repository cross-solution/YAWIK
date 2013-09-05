
;(function($) {
	
	initPagination = function()
	{
		$('.pagination li[class!="disabled"] a').click(loadPage);
	};
	
	loadPage = function(event)
	{
		var url = $(event.target).attr('href');
		$('#jobs-list-container').load(url, initPagination);
		
		return false;
	};
	
	$(function() { initPagination(); });
	
})(jQuery);
