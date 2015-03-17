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

    var $container;
    var modals = {};

    function onButtonClick(event)
    {
        event.preventDefault();
        var $a = $(event.currentTarget);

        console.debug($a, $a.data('id'));

        console.debug(modals, $a.data('id') in modals);

        if (! ($a.data('id') in modals)) {
            console.debug('Create modal for ' + $a.data('id'));
            var modal = new BootstrapDialog({
                type: BootstrapDialog.TYPE_DEFAULT,
                title: $a.data('title'),
                message: function(dialog) {
                    var $msg = $('<div>' + $a.data('loading') + '</div>');
                    $msg.load($a.attr('href'), function(response, status, xhr) {
                        if ('error' == status) {
                            var err = 403 == xhr.status ? 'Access denied!' : 'Application error!';
                            dialog.getModalBody().html(err);
                        }
                        dialog.getModalBody().find('select').select2();
                    });
                    return $msg;
                },
                data: {
                    href: $a.attr('href')
                },
                buttons : [{
                    label: 'Cancel',
                    cssClass: 'btn btn-default',
                    action: function(dialog) {
                        dialog.close();
                    }
                },{
                    label: 'Save',
                    cssClass: 'btn btn-primary',
                    action: function(dialog) {
                        var href = dialog.getData('href');
                        var userId = dialog.getModalBody().find('select').val();

                        $.post(href, {userId:userId}, null, 'json')
                            .done(function(data) {
                                if (data.success) {
                                    dialog.close();
                                } else {
                                    dialog.getModalBody().html(data.err);
                                }
                            })
                            .fail(function(xhr) {
                                var err = 403 == xhr.status ? 'Access denied!' : 'Application error!';
                                dialog.getModalBody().html(err);
                            })
                    }
                }]
            });
            modals[$a.data('id')] = modal;
        } else {
            console.debug('Modal already here: ' + $a.data('id'));
            modal = modals[$a.data('id')];
        }

        modal.realize();
        modal.open();

    }

    $(function() {
        $('#jobs-list-container').on('click', '.assign-user-button', onButtonClick);
    });

})(jQuery); 
 
