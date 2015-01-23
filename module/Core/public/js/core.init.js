(function ($) {

    initLanguageSwitcher = function () {
        $("#language-switcher button").click(function (e) {
            var switchToLang = '/' + $(this).attr("id").replace(/^language-switcher-/, "");

            if (lang != switchToLang) {
                var langRegex = new RegExp('/' + lang + '($|\/)');

                var newHref = location.protocol
                    + "//" + location.host
                    + location.pathname.replace(langRegex, switchToLang + '$1')
                    + location.search;
                //console.log(newHref);
                location.href = newHref;
            }
        });
    };
    initPnotify = function () {
        PNotify.prototype.options.styling = "fontawesome";
    };

    $(function () {
        initLanguageSwitcher();
        initPnotify();
    });

})(jQuery);