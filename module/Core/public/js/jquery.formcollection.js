

(function($) {
	
	var addButtonClickListener = function(e) {
		
		var $fieldset = $(e.data.fieldset);
	    var template = $('.form-collection-items > span', $fieldset)
	                   .data('template')
	                   .replace(/(fieldset-wrapper)/, '$1 hidden');
	    
	    var $contentDiv = $fieldset.find('.form-collection-items');
	    
	    
	    // find smallest free index number.
	    var index = 0;
	    var idPrefix = $contentDiv.attr('id').replace(/items$/, '');
	    while ($contentDiv.find('#' + idPrefix + index + '-wrapper').length) {
	    	index += 1;
	    }
	    
	    var $content = $(template.replace(/__index__/g, index));
	    $contentDiv.append($content);
	    initRemoveButtons($content);
	    $content.initform();
	    $content.css({ opacity: 0 }).slideDown().animate({ opacity: 1 }, { queue: false, duration: 'slow' }); //slideDown();
		return false;

	};
	
	var removeButtonClickListener = function(e) 
	{
		var itemId = "#" + $(e.target).attr('id').replace(/^remove-/, '') + '-wrapper';
		$(itemId).animate({height: 0, opacity: 0}, function() { $(this).remove() });
		
		return false;
	};
	
	var initRemoveButtons = function(parent)
	{
		parent.find('button.remove-collection-item-button')
			  .button({ icons: { primary: 'ui-icon-close' }, text: false })
			  .on('click.formcollection', removeButtonClickListener);
	};
	
	$.fn.formcollection = function( ) {
		
		return this.each(function() {
			if (!$(this).is('.form-collection')) return;
			
			$(this).find('.form-collection-add-item button').on(
				'click.formcollection',
				{ fieldset: this },
				addButtonClickListener
			);
			initRemoveButtons($(this));
		});
	};
	
	
})(jQuery);