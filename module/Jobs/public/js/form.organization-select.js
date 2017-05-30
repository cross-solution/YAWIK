/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * This plugin handles the Select2 box for organization.
 * The Organization select field must have attribute "data-element" with the value "organization-select"
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {

    function displayResult(item)
    {
        var data = parseTextToJson(item.text);
        var address = (data.city ? data.city + ', ' : '')
                    + (data.street ? data.street + ' ' + data.number : '');

        return $('<strong>' + data.name + '</strong><br><small>' + address + '</small>');
    }

    function displaySelection(item)
    {
        var data = parseTextToJson(item.text);

        return data.name;
    }

    function parseTextToJson(text)
    {
        var textArr = text.split('|');

        return {
            name: textArr[0],
            city: textArr[1],
            street: textArr[2],
            number: textArr[3]
        };
    }


    $(function() {
        $('select[data-element="organization-select"]').each(function() {

            var $select = $(this);
            var data = $select.data();
            var options = {
                allowClear: true,
                theme:"bootstrap",
                placeholder: { id: "0", text: data.placeholder },
                templateResult: displayResult,
                templateSelection: displaySelection
            };
            if (data.ajax) {
                options.ajax = {
                    url: basePath + '/' + data.ajax,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        console.debug(params);
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        params.page = params.page || 1;

                        return {
                            results: data.items,
                            pagination: {
                                more: (params.page * 30) < data.count
                            }
                        };
                    },
                    cache: true
                };
            }

            console.debug(options);
            $select.select2(options);
        });
    });

})(jQuery);

