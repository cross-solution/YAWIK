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
(function ($) {

    function showModal(event)
    {
        var $tr = $(event.currentTarget);
        var modal = $tr.data('modal');

        if (modal) {
            modal.open();
            return;
        }

        modal = new BootstrapDialog({
            closable: true,
            closeByBackdrop: true,
            autodestroy: false,
            title: $.fn.orderDetailModal.i18n.headline
                       .replace(/%1\$s/, $tr.data('ordernumber'))
                       .replace(/%2\$s/, $tr.data('ordertype')),
            message: $('<div>' + $.fn.orderDetailModal.i18n.loading + ' <span class="yk-icon fa-spinner fa-spin"></span></div>')
                     .load(basePath + '/' + lang + '/orders/view?id=' + $tr.data('orderid')),
            buttons: [
                {
                    label: $.fn.orderDetailModal.i18n.button,
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }
            ]
        });

        $tr.data('modal', modal);
        modal.realize();
        modal.getModalDialog().css('width', '95%');
        modal.open();
    }

    $.fn.orderDetailModal = function()
    {
        return this.each(function() {
            $(this).on('click', 'tbody tr', showModal);
        });
    };

    $.fn.orderDetailModal.i18n = {
        'headline': '[ %2$s ] Order %1$s',
        'loading' : 'Loading &hellip;',
        'button'  : 'Close'
    };

    /* document ready */
    $(function() {
        $('#orders-list-container').orderDetailModal();
    });

})(jQuery);
 
