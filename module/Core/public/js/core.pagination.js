
;(function($) {
	
	paginate = function(event)
	{
                //c/onsole.log('paginate-event2', event); 
                if (typeof event.data.loadingIndicator != 'undefined') {
                    event.data.loadingIndicator.show();
                }
                
                var url        = event.data.href;
                var $container;
                if (typeof event.data.container != 'undefined') {
                    $container = event.data.container;
                }
                else {
                    $container = $(event.currentTarget);
                }
                
                if (typeof url ==  'undefined' || 0 == url.length) {
                    //c/onsole.log('test.data-uri', $container, $container.data(), $container.data('uri'));
                    url = $container.data('uri');
                }
                else {
                    $container.data('uri', url);
                }
		$container.load(url, function(data) {
                    $container.pagination(); 
                    if (typeof event.data.loadingIndicator != 'undefined') {
                        event.data.loadingIndicator.hide();
                    }
                    // Use more verbose event name
                    $container.trigger('yk-pagination-loaded',  {data: data});
                    $container.trigger('ajax.ready', {'data': data});
                    
                });
		return false;
	};
	
	$.fn.pagination = function()
	{
                this.each(function() {
                    //
                    $(this).unbind('paginate');
                    $(this).bind('paginate', function(event, data) {
                        //c/onsole.log('paginate-data', event, data);
                        //data
                        event.data = data;
                        paginate(event);
                    });
                });
		return this.each(function() {
                    // default trigger
			
			var eventData = {
				"container": $(this),
				"loadingIndicator": $(this).find('.pagination-loading')
			};
			
			eventData.loadingIndicator.hide();
			
			$(this).find('.pagination li[class!="disabled"] a, th > a')
			       .click(eventData, function(e) {
                                    //c/onsole.log('paginate-event', e, eventData);
                                    var data = {};
                                    data.href = $(e.currentTarget).attr('href');
                                    data.container = eventData.container;
                                    data.container.trigger('paginate', [data]);
                                    //paginate(e);
                                    return false;
                                });
		});
	};
	
        // start with the default class
	$(function() {
		$(".pagination-container").pagination();
	});
	
})(jQuery);
