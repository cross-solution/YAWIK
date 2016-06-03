

(function($) {
	
	var selector = '.form-collection-container';
	
	var addButtonClickListener = function(e) {
		
		var $button = $(this);
		var $fieldset = $button.closest(selector);
	    var template = $fieldset.children('span[data-template]')
	                   .data('template');
	    var index = $fieldset.children('.sf-container').length;
	    var $content = $(template.replace(/__index__/g, index));
	    $fieldset.append($content);
//	    initRemoveButtons($fieldset);
//	    $fieldset.find(selector).formcollectionContainer();
	    
	    $(".sf-container").summaryform();
        $("a").click($.fn.summaryform.ensureSave);
        $('form:not([data-handle-by]), form[data-handle-by="yk-form"]').form();
	    
		return false;
	};
	
	var removeButtonClickListener = function(e) 
	{
		var $target = $(e.currentTarget).parent();
		
//		$target.fadeOut(function() { $target.remove() });
		
		return false;
	};
	
	var initAddButtons = function (parent) {
		parent.find('.form-collection-container-add').on('click.formcollectionContainer', addButtonClickListener);
	};
	
	var initRemoveButtons = function (parent) {
        parent.find('.form-collection-container-add').on(
            'click.formcollectionContainer',
            removeButtonClickListener
        );
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