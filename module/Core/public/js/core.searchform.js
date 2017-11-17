/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2016 CROSS Solution <http://cross-solution.de>
 */

/**
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($, win) {

    function resetSearchForm(event)
    {

        var $form = $(event.target);
        console.debug('resetsearchform', event.data);

        if ("native" === event.data.handleBy) {
            var uri = $form.attr('action');
            uri += (uri.match(/\?/) ? '&' : '?') + 'clear=1';
            window.location.href = uri;
            return false;
        }

        win.setTimeout(function() {
            $(':input', $form).each(function() {

                var $input = $(this);
                var type = this.type;
                var tag = this.tagName.toLowerCase(); // normalize case

                if (type == 'text' || type == 'password' || tag == 'textarea') {
                    this.value = "";
                    $input.change();

                } else if (type == 'checkbox' || type == 'radio') {
                    this.checked = false;
                    $input.change();

                } else if (tag == 'select') {
                    var selected = -1;
                    if (true === $input.data('clearOnSelect')) {
                        $input.html('');
                    } else {
                        $input.find('option[selected]').each(function () {
                            selected = $(this).prop('index');
                        });
                    }
                    this.selectedIndex = selected;

                    $input.trigger('change', {isSelect2Change: true});
                }
            });

            loadPaginator($form, true);
        }, 100);
    }

    function submitSearchForm(event, flags)
    {
        flags = flags || {};

        if (flags.isSelect2change) {
            return false;
        }

        if ('native' === event.data.handleBy && !flags.forceAjax) {
            return true;
        }

        var $form = $(event.target);
        loadPaginator($form);

        return false;
    }

    function loadPaginator($form, reset)
    {
        var paginatorId = $form.data('paginator-id');
        var $paginator = paginatorId ? $('#' + paginatorId) : $($('.pagination-container')[0]);
        var uri = $paginator.data('uri');
        var uriParts = uri.split('?');
        var baseUri = uriParts[0];
        var paginatorQuery = 1 < uriParts.length ? parseQueryString(uriParts[1], ['page', 'count']) : {};
        var formQuery      = parseQueryString(serializeForm($form), [ 'submit', 'reset' ], /*exclude*/ true);
        var query          = $.extend(reset ? {'clear':1} : {}, paginatorQuery, formQuery, {page: 1});
        //var queryStr       = $.param(query);

        console.debug(paginatorQuery, formQuery, query);

        $paginator.paginationContainer('load', baseUri + '?' + toQuery(query));
    }

    function toQuery(data, encode)
    {
        var queryParts = [];
        $.each(data, function(name, value) {
            value=encodeURIComponent(value);
            name=encodeURIComponent(name);
            queryParts.push(name + '=' + value);
        });

        return queryParts.join('&');
    }

    function serializeForm($form, exclude)
    {
        var data = $form.serializeArray();
        var processed = [];
        var parsed = {};
        var multiValues = $form.data('multivalues') || {};

        $.each(data, function(i, item) {
            if (-1 !== $.inArray(item.name, processed)) { return; }

            if (item.name.match(/\[\]$/)) {
                var $element = $form.find('select[name="' + item.name + '"]');
                var parsedName = item.name.slice(0,-2);
                var separator = multiValues.hasOwnProperty(parsedName) ? multiValues[parsedName] : ',';

                if ($element.length) {
                    var value = $element.val();
                } else {
                    var value = [];
                    $form.find('[name="' + item.name + '"]:checked').each(function() {
                        value.push($(this).val());
                    });
                }

                parsed[separator+parsedName] = value.join(separator);
                processed.push(item.name);

            } else {
                parsed[item.name] = item.value;
            }
        });

        return toQuery(parsed);

    }

    function parseQueryString(queryStr, filter, exclude)
    {
        //queryStr = queryStr.replace(/\%26/g, '__and__');
        //queryStr = decodeURIComponent(queryStr);
        var vars = queryStr.split('&');
        var data = {};

        for (i=0, c=vars.length; i<c; i++) {
            vars[i] = vars[i].replace(/__and__/g, '&');
            var varParts = vars[i].split('=');
            var name=decodeURIComponent(varParts[0]);
            var value=decodeURIComponent(varParts[1]);
            if ((exclude && -1 != $.inArray(name, filter))
                || (!exclude && -1 == $.inArray(name, filter))
            ) { continue; }

            data[name] = value;
        }

        return data;
    }

    $.fn.searchForm = function() {
        return this.each(function() {
            var $form = $(this);
            var searchParams = $form.data('search-params');

            if (searchParams) {
                for (var key in searchParams) {
                    if (searchParams.hasOwnProperty(key)) {
                        var name = key + ($.isArray(searchParams[key]) ? '[]' : '');
                        $form.find('[name="' + name + '"]').val(searchParams[key]);
                    }
                }
                $form.find('select').trigger('change', {isSelect2Change: true});
            }

            var data = $form.data();
            if ('native' === data.handleBy) {
                $form.append('<input type="hidden" name="clear" value="1">');
            }

            $form
                .on('reset.yk.core.search-form', data, resetSearchForm)
                .on('submit.yk.core.search-form', data, submitSearchForm)
                .on('change.yk.core.search-form', '[data-submit-on-change="true"]', data, submitSearchForm)
                .on('click.yk.core.search-form', '[data-submit-on-click="true"]', data, submitSearchForm)
            ;


        });
    };

    $(function() {
        $('.search-form').searchForm();

    })
})(jQuery, window);
 
