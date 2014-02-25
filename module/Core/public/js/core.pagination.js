
;(function($) {
	
	paginate = function(event)
	{
		event.data.loadingIndicator.show();
		var url        = $(event.currentTarget).attr('href');
		var $container = event.data.container;
		
		$container.load(url, function() { $container.pagination(); event.data.loadingIndicator.hide(); });
		return false;
	};
	
	$.fn.pagination = function()
	{
		return this.each(function() {
			
			var eventData = {
				"container": $(this),
				"loadingIndicator": $(this).find('.pagination-loading')
			};
			
			eventData.loadingIndicator.hide();
			
			$(this).find('.pagination li[class!="disabled"] a, th a')
			       .click(eventData, paginate);
		});
	};
	
	$(function() {
		$(".pagination-container").pagination();
	});
	
})(jQuery);
