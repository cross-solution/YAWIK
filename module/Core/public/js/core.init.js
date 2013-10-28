
(function($) {
	
	initLanguageSwitcher = function()
	{
		$("#language-switcher button").click(function(e) {
			var switchToLang = $(this).attr("id").replace(/^language-switcher-/, "");
			
			if ("' . $this->params('lang') . '" != switchToLang) {
				var langRegex = new RegExp('/' + lang);
				
				var newHref=location.protocol
                       + "//" + location.host + "/"
                       + location.pathname.replace(langRegex, switchToLang)
                       + location.search;
        
				location.href=newHref;
			}
        });
	};
	
	$(function() {
		initLanguageSwitcher();
	});
	
})(jQuery);