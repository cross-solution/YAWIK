

(function($) {
	
	var addButtonClickListener = function(e) {
		
		var $fieldset = $(e.data.fieldset);
	    var template = $('span', $fieldset)
	                   .data('template');
	   
	    // find smallest free index number.
	    var elementName = $fieldset.attr('id');
	    var index = 0;
	    
	    while($fieldset.find('#'+elementName+'-'+index).length) {
			index += 1;
		}
	    
	    var $content = $(template.replace(/__index__/g, index));
	    $fieldset.find('legend').after($content);
	    initButtons($content);
	    
	    $content.hide().fadeIn();
		return false;

	};
	
	var removeButtonClickListener = function(e) 
	{
		var $fieldset = $(e.data.fieldset);
		var $target   = $(e.currentTarget).parent();
		
		$target.fadeOut(function() { $target.remove() });
		//.hide().remove();
		
		return false;
	};
	
	
	var initButtons = function(parent)
	{
		parent.find('a.add-item').on(
				'click.formcollection',
				{ fieldset: parent },
				addButtonClickListener
		);
		parent.find('a.remove-item').on(
				'click.formcollection', 
				{ fieldset: parent },
				removeButtonClickListener
		);
	}
	
	$.fn.formcollection = function( ) {

		return this.each(function() {
			var collection = $(this);
			if (!collection.is('.form-collection')) return;
			
			initButtons(collection);
			
		});
	};
	
	$(function() { $('.form-collection').formcollection(); });
	
	
})(jQuery);