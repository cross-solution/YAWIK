

(function($) {
	
	var selector = '.form-collection-container';
	
	var addButtonClickListener = function() {
		var $button = $(this),
			$fieldset = $button.closest(selector),
			template = $fieldset.children('span[data-template]').data('template'),
			placeholder = $fieldset.data('template-placeholder'),
			index = $fieldset.find('.form-collection-container-form').length,
			$content = $(template.replace(new RegExp(placeholder, 'g'), index)),
			$addWrapper = $fieldset.find('.form-collection-container-add-wrapper');
		
		$content.insertBefore($addWrapper)
			.hide()
			.fadeIn();
		
		// following lines should be solved via triggering global init event or something like that
		$content.find(".sf-container").summaryform();
		$content.find("a").click($.fn.summaryform.ensureSave);
		$content.find('form:not([data-handle-by]), form[data-handle-by="yk-form"]').form();
	    
		return false;
	};
	
	var removeButtonClickListener = function(e) 
	{
		var $target = $(e.currentTarget).parent();
		
		return false;
	};
	
	var initAddButtons = function (parent) {
		parent.find('.form-collection-container-add-button').on('click.formcollectionContainer', addButtonClickListener);
	};
	
	var initRemoveButtons = function (parent) {
        
    };
	
	$.fn.formcollectionContainer = function( ) {

		return this.each(function() {
			var collection = $(this);
			if (!collection.is(selector)) return;
			
			initAddButtons(collection);
			initRemoveButtons(collection);
			
		});
	};
	
	$(function() {
		$(selector).formcollectionContainer();
	});
	
})(jQuery);