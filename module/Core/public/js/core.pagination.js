
;(function($) {
	
	paginate = function(event)
	{
		$.loadingPopup.show();
		var url        = $(event.target).attr('href');
		var $container = event.data.container;
		
		$container.load(url, function() { $container.pagination(); $.loadingPopup.hide(); });
		return false;
	};
	
	$.fn.pagination = function()
	{
		return this.each(function() {
			$(this).find('.pagination li[class!="disabled"] a, th a')
			       .click({"container": $(this)}, paginate);
		});
	};
	
	$(function() {
		$(".pagination-container").pagination();
	});
	
})(jQuery);
