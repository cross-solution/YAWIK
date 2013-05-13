

(function($) {
	
	var checkboxClickListener = function(e) {
		console.debug($(e.data.wrapper));
		$(e.data.wrapper).find('.form-element-date').each(function() {
			console.debug(this);
			if ($(this).find('div.daterange-currenttext').length) {
				if ($(e.target).prop('checked')) {
					$(this).find('div.daterange-currenttext').removeClass('hidden');
					$(this).find('input').addClass('hidden');
				} else {
					$(this).find('div.daterange-currenttext').addClass('hidden');
					$(this).find('input').removeClass('hidden');
				}
			}
		});
		
		
	};
	
	$.fn.daterange = function( ) {
		
		return this.each(function() {
			if ($(this).hasClass('form-element-daterange')) {
				console.debug('YIIHA!');
				$(this).find('.form-element-checkbox input').on('click.daterange', { wrapper: this }, checkboxClickListener);
			}
		});
		
	};
	
	
})(jQuery);