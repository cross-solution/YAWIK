

(function($) {
	
	var addButtonClickListener = function(e) {
		
		var $fieldset = $(e.data.fieldset);
	    var template = $fieldset.children('span[data-template]')
	                   .data('template');
	   
	    // find smallest free index number.
	    var elementName = $fieldset.attr('id');
	    var index = 0;
	    
	    while($fieldset.find('#'+elementName+'-'+index).length) {
			index += 1;
		}
	    
	    var $content = $(template.replace(/__index__/g, index));
	    $fieldset.children('legend').after($content);
	    initRemoveButtons($fieldset);
	    $fieldset.find('.form-collection').formcollection();
	    
	    $content.hide().fadeIn();
		return false;

	};
	
	var removeButtonClickListener = function(e) 
	{
		var $target   = $(e.currentTarget).parent();
		
		$target.fadeOut(function() { $target.remove() });
		
		return false;
	};
	
	var initAddButtons = function (parent) {
		parent.find('> legend a.add-item').on(
				'click.formcollection',
				{ fieldset: parent },
				addButtonClickListener
		);
	};
	
	var initRemoveButtons = function (parent) {
        parent.find('> fieldset > a.remove-item').on(
            'click.formcollection',
            removeButtonClickListener
        );
    };
	
	$.fn.formcollection = function( ) {

		return this.each(function() {
			var collection = $(this);
			if (!collection.is('.form-collection')) return;
			
			initAddButtons(collection);
			initRemoveButtons(collection);
			
		});
	};
	
	$(function() { $('.form-collection').formcollection(); });
	
	
})(jQuery);