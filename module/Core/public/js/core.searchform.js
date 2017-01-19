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
        win.setTimeout(function() { loadPaginator($form, true); $form.find('select').trigger('change', [ true ]); }, 100);
    }

    function submitSearchForm(event, isSelect2change)
    {
        if (!isSelect2change) {
            var $form = $(event.target);
            loadPaginator($form);
        }

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
        var formQuery      = parseQueryString($form.serialize(), [ 'submit', 'reset' ], /*exclude*/ true);
        var query          = $.extend(reset ? {'clear':1} : {}, paginatorQuery, formQuery, {page: 1});
        var queryStr       = $.param(query);

        $paginator.paginationContainer('load', baseUri + '?' + queryStr);
    }

    function parseQueryString(queryStr, filter, exclude)
    {
        var vars = queryStr.split('&');
        var data = {};

        for (i=0, c=vars.length; i<c; i++) {
            var varParts = vars[i].split('=');
            if ((exclude && -1 != $.inArray(varParts[0], filter))
                || (!exclude && -1 == $.inArray(varParts[0], filter))
            ) { continue; }

            data[varParts[0]] = varParts[1];
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
                        $form.find('[name="' + key + '"]').val(searchParams[key]);
                    }
                }
                $form.find('select').trigger('change', [ true ]);
            }

            $form.on('reset.yk.core.search-form', resetSearchForm)
                 .on('submit.yk.core.search-form', submitSearchForm)
                 .on('change.yk.core.search-form', '[data-submit-on-change="true"]', submitSearchForm)
                 .on('click.yk.core.search-form', '[data-submit-on-click="true"]', submitSearchForm);

        });
    };

    $(function() {
        $('.search-form').searchForm();

    })
})(jQuery, window);
 
