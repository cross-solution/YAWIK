/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * This plugin handles the Select2 box for multiposting
 *
 * Author: Carsten Bleek <bleek@cross-solution.de>
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
        var $select = $('#jobPortals-channel');
        var data = $select.data();
        var $eventSelect = $(".js-example-events");

        $select.select2({
            allowClear: false,
            placeholder: data.placeholder,
            formatResult: displayResult,
            formatSelection: displaySelection
        });
    });

})(jQuery);

