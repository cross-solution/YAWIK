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
        if (item.children) {
            return item.text;
        }

        console.debug(item);
        var data = parseTextToJson(item.text);
    console.debug(data);

        var link = '<a href="' + data.link + '">' + data.linkText + '</a>';
        var desc = data.desc.replace(/%s/, link);

        return $('<strong>' + data.name + ' - ' + data.headline + '</strong><br><small>' + desc + '</small>');
    }

    function displaySelection(item)
    {
        var data = parseTextToJson(item.text);

        return data.name + ' ( ' + data.duration + ' )';
    }

    function parseTextToJson(text)
    {
        var textArr = text.split('|');

        return {
            name: textArr[0],
            headline: textArr[1],
            desc: textArr[2],
            linkText: textArr[3],
            link: textArr[4],
            duration: textArr[5]
        };
    }


    $(function() {
        var $select = $('#jobPortals-channel');
        var data = $select.data();
        //var $eventSelect = $(".js-example-events");

        $select.select2({
            //allowClear: true,
            placeholder: data.placeholder,
            formatResult: displayResult,
            formatSelection: displaySelection
        });
        console.debug($select);
    });

})(jQuery);

