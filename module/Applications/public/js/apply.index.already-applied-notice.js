/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * Handles the displaying of the Already Applied warning alert.
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 * Since: 0.20
 */
;
(function ($) {

    /**
     * The alert div
     */
    var $container;

    /**
     * Hides or shows the alert div.
     *
     * Event-Listener for "done.yk.core.forms"
     *
     * @param e the event.
     * @param result Additional parameters passed by the trigger.
     *               Should contain a property "data" which is an object
     *               that has a property "hasApplied" with a boolean value.
     */
    function toggleContainer(e, result)
    {
        if (result.data.hasApplied) {
            $container.show();
        } else {
            $container.hide();
        }
    }

    /**
     * Document ready handler.
     * Initializes the alert div (set initial state)
     * Binds the listener to the "done.yk.core.forms" event of every form in the page.
     */
    $(function() {
        $container = $('#already-applied-msg');
        if (false == $container.data('show')) { $container.hide(); }

        $('form').on('done.yk.core.forms', toggleContainer);
    });

})(jQuery);
 
