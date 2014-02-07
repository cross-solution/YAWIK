
(function($) {

    initLanguageSwitcher = function()
    {
        $("#language-switcher button").click(function(e) {
            var switchToLang = '/' + $(this).attr("id").replace(/^language-switcher-/, "");

            if (lang != switchToLang) {
                var langRegex = new RegExp('/' + lang);

                //console.log(location.pathname, location.pathname.replace(langRegex, switchToLang));
                var newHref = location.protocol
                        + "//" + location.host
                        + location.pathname.replace(langRegex, switchToLang)
                        + location.search;
                //console.log(newHref);
                location.href = newHref;
            }
        });
    };

    $(function() {
        initLanguageSwitcher();
    });

})(jQuery);