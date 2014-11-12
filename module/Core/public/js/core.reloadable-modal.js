/**
 *
 */

;(function($, document) {


    function genStrHash(str)
    {
        var hash = 0, i, chr, len;
        if (str.length == 0) return hash;
        for (i = 0, len = str.length; i < len; i++) {
            chr   = str.charCodeAt(i);
            hash  = ((hash << 5) - hash) + chr;
            hash |= 0; // Convert to 32bit integer
        }
        return hash;
    }



    function reloadModalContent(e)
    {
        if (e.isDefaultPrevented()) return;

        var $modal = e.data.modal;
        var href   = e.data.href;

        if (!$modal.data('original-html')) {
            $modal.data('original-html', $modal.html());
        } else {
            $modal.html($modal.data('original-html'));
        }

        if (href) {
            if ($modal.data('bs.modal').options.usePost) {
                var postData = $modal.data('bs.modal').options.postData;
                var cacheKey = 'cache-post-' + href + '-' + genStrHash($.param(postData));

                if ($modal.data(cacheKey)) {
                    $modal.html($modal.data(cacheKey));
                    return;
                }
                var promise = $.post(href, postData);
            } else {
                var cacheKey = 'cache-get-' + href;
                if ($modal.data(cacheKey)) {
                    $modal.html($modal.data(cacheKey));
                    return;
                }
                var promise = $.get(href);
            }

            promise
             .done(function ( data ) { replaceModalContent($modal, data); $modal.data(cacheKey, $modal.html()); })
             .fail(function ( jqXHR, textStatus, errorThrown) { displayLoadError($modal, errorThrown); });
        }
    }

    function replaceModalContent($modal, data)
    {
        var reloadable = $modal.data('reloadable');

        if (true === reloadable) {
            $modal.html(data);
            return;
        }

        var html = '<div>' + data + '</div>';
        var $html = $(html);
        var found = false;
        $html.each(function() {
            var $htmlElement = $(this);
            $.each(['header', 'title', 'body', 'footer'], function(i, name) {
                var className = '.modal-' + name;
                var $elem = $htmlElement.hasClass(className.substr(1))
                    ? $htmlElement
                    : $htmlElement.find(className);

                if ($elem.length) {
                    found = true;
                    var $orig = $modal.find(className);
                    if ($orig.length) {
                        $orig.html($elem.html());
                    }
                }
            });
        });

        if (!found && $modal.find('.modal-' + reloadable).length) {
            $modal.find('.modal-' + reloadable).html(data);
        }

    }

    function displayLoadError($modal, err)
    {
        var title = $modal.data('error-title');
        var body  = $modal.data('error-message');

        if (!title) {
            title = 'Error loading content.';
        }
        if (!body) {
            body = '<p>An error occured while fetching the content of this modal box.</p>';
        }

        $modal.find('.modal-title').html(title);
        $modal.find('.modal-body').html(body);
    }


    function Modal (option, _relatedTarget)
    {
        var $modal = this;
        return this.each(function () {
            var $this   = $(this);

            if (!$this.data('reloadable')) {
                twbsModal.call($modal, option, _relatedTarget);
                return;
            }

            if (option.hasOwnProperty('remote')) {
                var href = option.remote;
                delete option.remote;
            } else {
                var href = $(_relatedTarget).attr('href');
            }
            $this.one('show.bs.modal', {modal: $this, href: href }, reloadModalContent);
            twbsModal.call($modal, option, _relatedTarget);
        });
    }

    var twbsModal = $.fn.modal;
    $.fn.modal = Modal;

    // We need to remove the default data-API handlers, because we need to alter the code.
    // ==============
    $(document).off('click.bs.modal.data-api', '[data-toggle="modal"]');


    // Register our altered handler
    $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
        var $this   = $(this);
        var href    = $this.attr('href');
        var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))); // strip for ie7
        var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data());

        if ($this.is('a')) e.preventDefault();

        $target.one('show.bs.modal', function (showEvent) {
            if (showEvent.isDefaultPrevented()) return; // only register focus restorer if modal will actually get shown
            $target.one('hidden.bs.modal', function () {
                $this.is(':visible') && $this.trigger('focus')
            })
        });

        // This is what we needed to change. Call our Modal function, not bootstraps!
        Modal.call($target, option, this);

    });

})(jQuery, document);
