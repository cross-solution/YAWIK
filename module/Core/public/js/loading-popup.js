;(function($) {
	$.loadingPopup = {
		show: function () 
		{
			$('#loading-popup').modal();
		},
		
		hide: function ()
		{
			$('#loading-popup').modal('hide');
		}
	};
})(jQuery);