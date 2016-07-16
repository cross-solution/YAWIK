
;(function($) {

    var methods = {
        init: function()
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
                        data.loadingIndicator = eventData.loadingIndicator;
                        data.container.trigger('paginate', [data]);
                        //paginate(e);
                        return false;
                    });
            });
        },

        reload: function()
        {
            return this.each(function() {
                var $container = $(this);
                $container.trigger('paginate', [{container:$container, "loadingIndicator": $container.find('.pagination-loading')}]);
            });
        },

        load: function(url)
        {
            return this.each(function() {
                var $container = $(this);
                var data = {
                    href: url,
                    container: $container,
                    "loadingIndicator": $(this).find('.pagination-loading')
                };
                $container.trigger('paginate', [data]);
            });
        }
    };
	
	function paginate(event)
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
	}
	
	$.fn.pagination = function(method)
	{
        method = method || 'init'; // set default method if none provided.

        return method in methods
               ? methods[method].apply(this, Array.prototype.slice.call(arguments, 1))
               : this; // Maybe add error handling? Currently it's an no-op to call undefined method.
	};
	
    // Automatically init elements with class ".pagination-container" on page load.
	$(function() {
		$(".pagination-container").pagination();
	});
	
})(jQuery);
