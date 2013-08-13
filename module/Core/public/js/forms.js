
;(function($) {
	
	function addItemButtonListener(e) {
		var fieldset = $(e.target).parent().parent();
		var template = fieldset.find('span').attr('data-template');
		var elementName = fieldset.attr('id');
		var index = 0;

		while(fieldset.find('fieldset#'+elementName+'-'+index).length) {
			index += 1;
		}
		console.debug(index);
		return false; // prevent default
	}
	
	$(function() {
		$('button.add-item').click(addItemButtonListener);
	});
	
})(jQuery);