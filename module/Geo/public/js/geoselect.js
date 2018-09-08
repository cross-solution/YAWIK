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
        if (data.loading || typeof data.data != 'object') {
            return data.text;
        }


        return '<strong>' + getName(data.data) + '</strong><br /><small>' + data.data.region + '</small>';

    }

    function formatSelection(data)
    {
        console.debug(data, typeof data.data);
        if (!data.id || typeof data.data != 'object') { return data.text; }

        return getName(data.data);
    }

    function getName(data)
    {
        var name = "";


        if (data.postalCode) {
            name += data.postalCode + " ";
        }

        name += data.city;

        return name;
    }

    function setupGeoSelect($node)
    {
        $node.select2({
            theme: 'bootstrap',
            width: $node.data('width'),
            placeholder: $node.data('placeholder'),
            minimumInputLength: 2,

            ajax: {
                url: basePath + '/',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        ajax: 'geo',
                        lang: lang
                    };
                },
                processResults: function(data, params) {
                    console.debug('processResults: results:', data);
                    console.debug($.map(data.items, function(item) { return {id: JSON.stringify(item), data: item}; }));
                    //params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) { return {id: JSON.stringify(item), data: item}; }),
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

        var initialValue = $node.data('val');



        if (!initialValue) {
            initialValue = [];
        } else if (!$.isArray(initialValue)) {
            initialValue = [initialValue];
        }

        console.debug(initialValue);
        if (initialValue.length) {
            for (var i=initialValue.length-1; i>=0; i-=1) {
                console.debug("initVal " + i + ": "+ initialValue[i]);
                var $option = $('<option selected>Test</option>');
                $option.val(initialValue[i]);
                $option.text(formatSelection({id: initialValue[i], data: JSON.parse(initialValue[i])}));
                $node.prepend($option);
            }
            $node.trigger('change');
        }
        
        $node.parents('form').on('reset.geoselect', function(event) {
            window.setTimeout(function() {
                $node.val('').trigger('change');
            }, 10)
        })
    }

    $.fn.geoSelect = function () {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('geoSelectInitialized');

            if (!data) {
                $this.data('geoSelectInitialized', true);
                setupGeoSelect($this);
            }
        });
    };
    
    $(function() {
        $('select.geoselect').geoSelect();
    });

})(jQuery); 
 
