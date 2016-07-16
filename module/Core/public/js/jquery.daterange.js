

(function($) {
	
	var checkboxClickListener = function(e) {
		var input = $(e.data.wrapper).find('[id$="-enddate"]');
		var div   = $(e.data.wrapper).find('div.daterange-currenttext');
		
		
		if ($(e.target).prop('checked')) {
			div.removeClass('hidden');
			input.addClass('hidden');
		} else {
			div.addClass('hidden');
			input.removeClass('hidden');
		}
	};
	
	$.fn.daterange = function( ) {
		
		return this.each(function() {
			if ($(this).hasClass('form-element-daterange')) {
				$(this).find('.form-element-checkbox input').on('click.daterange', { wrapper: this }, checkboxClickListener);
			}
		});
		
	};
	
	
})(jQuery);