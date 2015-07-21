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

    var resultTmpl = null;
    var selectTmpl = null;
    var numberFormat = {
        'delimiter': ',',
        'decimal': '.'
    };


    function displayResult(item)
    {
        if (item.children) {
            return item.text;
        }

        var data = $.fn.multipostingSelect.getOptionData(item.text);

        var link = '<a href="' + data.link + '">' + data.linkText + '</a>';
        data.desc = data.desc.replace(/%s/, link);

        return tmpl(resultTmpl, data);
    }

    function displaySelection(item)
    {
        var data = $.fn.multipostingSelect.getOptionData(item.text);

        return tmpl(selectTmpl, data);
    }

    function updatePrice(e)
    {
        var $select  = $(e.target);
        var selected = $select.find('option:selected');
        var sum      = $.fn.multipostingSelect.calculatePrice(selected);
        var price    = $.fn.multipostingSelect.formatPrice(sum, numberFormat);

        $('#' + $select.attr('id') + '-total span').text(price);

    }

    function tmpl(template, vars)
    {
        for (var key in vars) {
            var search = new RegExp('%' + key, 'gi');
            template = template.replace(search, vars[key]);
        }

        return template;
    }

    $(function() {
        var $select = $('#jobPortals-portals');
        var data = $select.data();

        // get templates
        var id = $select.attr('id');
        resultTmpl = $('span#' + id + '-result-tmpl').data('template');
        selectTmpl = $('span#' + id + '-select-tmpl').data('template');
        numberTmpl = $('span#' + id + '-currency-tmpl').data('template');
        numberFormat.delimiter = numberTmpl.substr(1,1);
        numberFormat.decimal   = numberTmpl.substr(5,1);

        $select.select2({
            //allowClear: true,
            placeholder: data.placeholder,
            formatResult: displayResult,
            formatSelection: displaySelection
        });

        $select.on("change", updatePrice);
        $select.trigger('change');
    });

    $.fn.multipostingSelect = {};
    $.fn.multipostingSelect.formatPrice = function(price, numberFormat)
    {
        price = price.toFixed(2)
            .replace(".", numberFormat.decimal)
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1" + numberFormat.delimiter);

        return price;

    };

            /*
                        s = 900, t = 1280, p = 400
                        s = 1300,
             */
    $.fn.multipostingSelect.calculatePrice = function(selectedOptions)
    {
        var sum = 0; var total = 0;

        for (var i= 0, c=selectedOptions.length; i<c; i+=1) {
            var data = $.fn.multipostingSelect.getOptionData($(selectedOptions[i]).text());
            sum += data.price;
            if (1 < c && sum < data.minPrice && total < data.minPrice) {
                total = data.minPrice;
            } else {
                total = sum > total ? sum : total;
            }
        }

        return total;
    };

    $.fn.multipostingSelect.getOptionData = function(text)
    {
        var textArr = text.split('|');

        return {
            name: textArr[0],
            headline: textArr[1],
            desc: textArr[2],
            linkText: textArr[3],
            link: textArr[4],
            duration: textArr[5],
            nicePrice: textArr[6],
            price: parseFloat(textArr[7]),
            minNicePrice: textArr[8],
            minPrice: parseFloat(textArr[9])
        };
    };

})(jQuery);

