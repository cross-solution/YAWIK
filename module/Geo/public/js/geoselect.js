/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2017 CROSS Solution <http://cross-solution.de>
 */

/**
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {

    function formatResult(data)
    {
        if (data.loading) {
            return data.text;
        }

        data = parseData(data);
        return '<strong>' + data.name + '</strong><br /><small>' + data.info + '</small>';

    }

    function formatSelection(data)
    {
        if (!data.id) { return data.text; }

        return data.name;
    }

    function parseData(data)
    {
        var info = '';

        if (data.city) { info += data.city; }
        if (data.state) { info += (info ? ', ' : '') + data.state; }

        return {
            name: data.name,
            info: info
        };
    }

    function setupGeoSelect($node)
    {
        $node.select2({
            theme: 'bootstrap',
            width: $node.data('width'),
            placeholder: $node.data('placeholder'),
            minimumInputLength: 2,

            ajax: {
                url: basePath + '/' + lang + '/' + $node.data('uri'),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function(data, params) {
                    console.debug('results:', data);
                    //params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: false
                        }
                    }
                }
            },
            templateResult: formatResult,
            templateSelection: formatSelection,
            escapeMarkup: function(m) { return m; }
        });
    }

    $(function() {
        $('select.geoselect').each(function() {
            var $select = $(this);
            setupGeoSelect($select);
            $select.parents('form').on('reset.geoselect', function(event) {
                window.setTimeout(function() {
                    $select.val('').trigger('change');
                }, 10)
            })
        });
    });


})(jQuery); 
 
