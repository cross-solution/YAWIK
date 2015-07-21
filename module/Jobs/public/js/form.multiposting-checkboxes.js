/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * This plugin handles the alternative checkbox like selection element for multiposting
 *
 * USES THE SAME NAME AS form.multiposting-select.js SO DO NOT USE AT THE SAME TIME!
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {

    var $container;
    var $buttons;
    var numberFormat = {
        'delimiter': ',',
        'decimal': '.'
    };

    function onButtonClick(e)
    {

        var $button  = $(e.target);
        var $input   = $button.find('input');

        $input.prop('checked', !$input.prop('checked'));
        if ($input.prop('checked')) {
            $button.addClass('btn-success').removeClass('btn-default');
        } else {
            $button.addClass('btn-default').removeClass('btn-success');
        }

        updatePrice();

    }

    function updatePrice()
    {
        var sum      = $.fn.multipostingSelect.calculatePrice();
        var price    = $.fn.multipostingSelect.formatPrice(sum, numberFormat);

        $('#' + $container.attr('id') + '-total span').text(price);
    }

    $(function() {
        $container = $('#jobPortals-portals');

        // get templates
        var id = $container.attr('id');
        numberTmpl = $('span#' + id + '-currency-tmpl').data('template');
        numberFormat.delimiter = numberTmpl.substr(1,1);
        numberFormat.decimal   = numberTmpl.substr(5,1);


        $buttons = $container.find('.mpc-button');
        $buttons.popover().click(onButtonClick);
        updatePrice();
    });

    $.fn.multipostingSelect = {};
    $.fn.multipostingSelect.formatPrice = function(price, numberFormat)
    {
        price = price.toFixed(2)
            .replace(".", numberFormat.decimal)
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1" + numberFormat.delimiter);

        return price;

    };

    $.fn.multipostingSelect.calculatePrice = function()
    {
        var sum = 0, total = 0, $activeButtons = $container.find('.mpc-button.btn-success');
        var c = $activeButtons.length;

        console.debug($activeButtons);
        $activeButtons.each(function() {
            var $button = $(this);
            var price   = $button.data('price');
            var minPrice = $button.data('minprice');

            sum += parseFloat(price);

            if (1 < c && sum < minPrice && total < minPrice) {
                total = minPrice;
            } else {
                total = sum > total ? sum : total;
            }


        });

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
            niceMinPrice: textArr[8],
            minPrice: textArr[9],

        };
    };

})(jQuery);

