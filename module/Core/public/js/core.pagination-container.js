
;(function($) {


    function PaginationContainer(node)
    {
        this.$container = $(node);
        this.init();
    }

    $.extend(PaginationContainer.prototype,
    {
        init: function()
        {
            var $loading = this.$container.find('.pagination-loading');
            var $message = this.$container.find('.pagination-message');
            var $error   = this.$container.find('.pagination-error');
            var $content = this.$container.find('.pagination-content');


            if (!$message.length) {
                var message = this.$container.data('message');
                if (!message) { message = '<strong>Sorry</strong>, your search yields no results.'; }
                $message = $('<div class="pagination-message alert alert-warning">' + message + '</div>');
                this.$container.prepend($message);
            }
            if (!$error.length) {
                var errMessage = this.$container.data('error');
                if (!errMessage) { errMessage = '<strong>Sorry</strong>, loading results failed.'; }
                $error = $('<div class="pagination-error alert alert-danger">' + errMessage + '</div>');
                this.$container.prepend($error);
            }
            if (!$loading.length) {
                $loading  = $('<div style="position:absolute; z-index:1000; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(250,250,250,0.5);" id="jobs-list-container-loading-indicator" class="pagination-loading"><i class="fa-spin yk-icon-spinner yk-icon fa-2x" style="position:absolute; top: 25%; left: 50%;"></i></div>');
                this.$container.prepend($loading);
            }

            $loading.hide();
            $error.hide();

            if (!$content.html().trim()) {
                $message.show();
                $content.hide();
            } else {
                $message.hide();
                $content.show();
            }

            this.$loading = $loading;
            this.$content = $content;
            this.$message = $message;
            this.$error   = $error;

            this.$container.data('container', this);
            this.$container.css({position:'relative'});
            this.initControlHandler();
        },

        initControlHandler: function()
        {
            this.$content
                .find('.pagination li[class!="disabled"] a, th > a')
                .click($.proxy(this.loadByControl, this));
        },

        loadByControl: function(e) {
            var $link = $(e.currentTarget);
            var url   = $link.attr('href');

            this.load(url);
            return false;
        },

        load: function(url)
        {
            this.$loading.show();
            this.$container.data('uri', url);
            $.get(url)
                .done($.proxy(this.loadSuccess, this))
                .fail($.proxy(this.loadFail, this));
        },

        reload: function()
        {
            var url = this.$container.data('uri');

            if (!url) return;

            this.load(url);
        },

        loadSuccess: function(data)
        {
            /* hide everything */
            this.$content.hide();
            this.$message.hide();
            this.$error.hide();

            this.$content.html(data);

            if ($.trim(data)) {
                this.$content.show();
                this.initControlHandler();
            } else {
                this.$message.show();
            }
            this.$loading.hide();
            this.$container.trigger('yk-paginator-container:loaded');
        },

        loadFail: function()
        {
            this.$content.html('').hide();
            this.$message.hide();
            this.$error.show();
            this.$loading.hide();
        }
    });


    var methods = {

        init: function()
        {
            return this.each(function() {
                methods.getContainer(this);
                //var container = new PaginationContainer(this);
            });
        },

        reload: function()
        {
            return this.each(function() {
                methods.getContainer(this).reload();
            });
        },

        load: function(url)
        {
            return this.each(function() {
                methods.getContainer(this).load(url);
            });
        },

        getContainer: function(node)
        {
            var $container = $(node);
            var container  = $container.data('container');

            if (!container) {
                container  = new PaginationContainer(node);
            }

            return container;
        }
    };
	
	$.fn.paginationContainer = function(method)
	{
        method = method || 'init'; // set default method if none provided.

        return method in methods
               ? methods[method].apply(this, Array.prototype.slice.call(arguments, 1))
               : this; // Maybe add error handling? Currently it's an no-op to call undefined method.
	};
	
    // Automatically init elements with class ".pagination-container" on page load.
	$(function() {
		$(".pagination-container").paginationContainer();
	});
	
})(jQuery);
