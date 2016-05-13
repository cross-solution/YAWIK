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
        win.setTimeout(function() { loadPaginator($form); $form.find('select').change(); }, 1);
    }

    function submitSearchForm(event)
    {
        var $form = $(event.target);
        loadPaginator($form);

        return false;
    }

    function loadPaginator($form)
    {
        var paginatorId = $form.data('paginator-id');
        var $paginator = paginatorId ? $('#' + paginatorId) : $($('.pagination-container')[0]);
        var uri = $paginator.data('uri');
        var uriParts = uri.split('?');
        var baseUri = uriParts[0];
        var paginatorQuery = 1 < uriParts.length ? parseQueryString(uriParts[1]) : {};
        var formQuery      = parseQueryString($form.serialize(), [ 'submit', 'reset' ]);
        var query          = $.extend({}, paginatorQuery, formQuery, {page: 1});
        var queryStr       = $.param(query);

        $paginator.paginationContainer('load', baseUri + '?' + queryStr);
    }

    function parseQueryString(queryStr, ignore)
    {
        var vars = queryStr.split('&');
        var data = {};

        for (i=0, c=vars.length; i<c; i++) {
            var varParts = vars[i].split('=');
            if (-1 == $.inArray(varParts[0], ignore)) {
                data[varParts[0]] = varParts[1];
            }
        }

        return data;
    }

    $.fn.searchForm = function() {
        return this.each(function() {
            var $form = $(this);
            var searchParams = $form.data('search-params');

            if (searchParams) {
                for (var key in searchParams) {
                    $form.find('[name="' + key + '"]').val(searchParams[key]);
                }
            }

            $form.on('reset.yk.core.search-form', resetSearchForm)
                 .on('submit.yk.core.search-form', submitSearchForm);

        });
    };

    $(function() {
        $('.search-form').searchForm();

    })
})(jQuery, window);
 
