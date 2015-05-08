/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * Handles the "Track applications" form (\Jobs\Form\AtsMode)
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 * Since: 0.19
 */
;
(function ($) {

    var $form;
    var $select;
    var $uri;
    var $email;

    function toggleAdditionalInputs()
    {
        var value = $select.val();

        $uri.slideUp();
        $email.slideUp();

        if ('uri' == value) {
            $uri.slideDown();
        } else if ('email' == value) {
            $email.slideDown();
        }
    }

    function reloadIframe()
    {
        $('iframe')[0].contentDocument.location.reload(true);
    }

    $(function() {
        $form   = $('#descriptionForm\\.atsMode');
        $select = $form.find('#atsMode-mode');
        $uri    = $form.find('#atsMode-uri').parent().parent();
        $email  = $form.find('#atsMode-email').parent().parent();

        console.debug($form, $select, $uri, $email);
        $select.change(toggleAdditionalInputs);
        $form.on('done.yk.core.forms', reloadIframe);
        toggleAdditionalInputs();
    })

})(jQuery); 
 
