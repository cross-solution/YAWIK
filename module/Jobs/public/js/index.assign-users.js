/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * Handles assignment of organization employees to jobs.
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;
(function ($) {

    var modals = {};

    function onButtonClick(event)
    {
        event.preventDefault();
        var $a = $(event.currentTarget),
            modal; // modal declared here to prevent duplicate declaration

        var dataText = $a.data('i18n');
        var text = dataText
                 ? $.extend({}, event.data.options["i18n"], dataText)
                 : event.data.options["i18n"];

        console.debug(text);
        if (! ($a.data('id') in modals)) {
            modal = new BootstrapDialog({
                type: BootstrapDialog.TYPE_DEFAULT,
                title: text.title,
                message: function(dialog) {
                    var $msg = $('<div>' + text.loading + '</div>');
                    $msg.load($a.attr('href'), function(response, status, xhr) {
                        if ('error' == status) {
                            var err = 403 == xhr.status ? text.accessError : text.appError;
                            dialog.getModalBody().html(err);
                        }
                        dialog.getModalBody().find('tr').click(function() {
                            console.debug(this);
                            $(this).find('input').prop('checked', true);
                            dialog.getModalBody().find('tr').removeClass('bg-primary');
                            $(this).addClass('bg-primary');
                        });
                    });
                    return $msg;
                },
                data: {
                    href: $a.attr('href')
                },
                buttons : [{
                    label: text.cancel,
                    cssClass: 'btn btn-default',
                    id: 'assign-user-close-button',
                    action: function(dialog) {
                        dialog.close();
                    }
                },{
                    label: text.save,
                    cssClass: 'btn btn-primary',
                    action: function(dialog) {
                        var $button = this;
                        $button.html(text.sending + '&hellip; <span class="yk-icon yk-icon-spinner fa-spin"></span>');
                        $button.disable();
                        dialog.getButton('assign-user-close-button').disable();
                        dialog.setClosable(false);

                        var href = dialog.getData('href');
                        var userId = null;
                        dialog.getModalBody().find('input[type="radio"]').each(function() {
                            if ($(this).prop('checked')) { userId = $(this).val(); }
                        });

                        $.post(href, {userId:userId}, null, 'json')
                            .done(function(data) {
                                if (data.success) {
                                    $('#jobs-list-container').paginationContainer('reload');
                                    dialog.close();
                                } else {
                                    dialog.getModalBody().html(data.err);
                                }
                            })
                            .fail(function(xhr) {
                                var err = 403 == xhr.status ? text.accessError : text.appError;
                                dialog.getModalBody().html(err);
                                $button.html(text.fail);
                                dialog.getButton('assign-user-close-button').enable();
                                dialog.setClosable(true);
                            })
                    }
                }]
            });
            modals[$a.data('id')] = modal;
        } else {
            modal = modals[$a.data('id')];
        }

        modal.realize();
        modal.open();

    }

    $.fn.ykAssignUsers = function( options )
    {
        var opts = $.extend({}, $.fn.ykAssignUsers.defaults, options);

        return this.each(function() {
            $(this).on('click', '.assign-user-button', {options: opts}, onButtonClick);
        });
    };

    $(function() {
        $('#jobs-list-container').ykAssignUsers();
    });

    $.fn.ykAssignUsers.defaults =
    {
        "i18n":
        {
            title: 'Changing responsible user.',
            loading: 'Loading&hellip;',
            accessError: 'Access denied!',
            appError: 'Application error!',
            cancel: 'Cancel',
            save: 'Save',
            sending: 'Sending',
            fail: 'Fail!'
        }
    };

})(jQuery);
