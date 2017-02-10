/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2017 CROSS Solution <http://cross-solution.de>
 */

/**
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 * initialise the language-switcher
 */

(function ($) {

    function formatState (state) {
        if (!state.id) { return state.text; }

        var $state = $(
            '<span><span class="flag-icon flag-icon-' + state.id.slice(-2) + '"></span> ' + state.text + '</span>'
        );
        return $state;
    };

    // language-switcher submits the language as en_us or de. last 2 chars are defining the flag, first 2 chars are
    // defining the route

    initLanguageSwitcher = function () {
        $("#language-switcher select").change(function (e) {

            var select = $(e.currentTarget);
            var switchToLang = '/' + select.val().slice(0,2);

            if (lang != switchToLang) {
                var langRegex = new RegExp('/' + lang + '($|\/)');

                var newHref = location.protocol
                    + "//" + location.host
                    + location.pathname.replace(langRegex, switchToLang + '$1')
                    + location.search;
                // console.log(newHref);
                location.href = newHref;
            }
        });
        $('.language-switcher').select2({
            templateResult: formatState,
            theme: 'bootstrap',
            templateSelection: formatState,
            minimumResultsForSearch: -1
        });
    };

    $(function () {
        initLanguageSwitcher();
    });

})(jQuery);
