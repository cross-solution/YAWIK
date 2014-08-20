
;(function($) {
	
	$(function() {
		$('form').on('yk.forms.done', function(e, args) {
	
			if ($.fn.spinnerbutton) {
				$(this).find('button[type="submit"]').spinnerbutton('toggle');
			}	
			$('#settings-form-response').html(args.data.content);
		});
	});
	
})(jQuery);