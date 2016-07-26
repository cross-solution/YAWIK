

(function($) {
	
	var selector = '.form-collection-container';
	
	var addButtonClickListener = function() {
		var $button = $(this),
			$collectionContainer = $button.closest(selector),
			template = $collectionContainer.children('span[data-template]').data('template'),
			placeholder = $collectionContainer.data('template-placeholder'),
			$content = $(template),
			$form = $content.find('form'),
			$addWrapper = $collectionContainer.find('.form-collection-container-add-wrapper');
		
		// add new form before add button
		$content.insertBefore($addWrapper)
			.hide()
			.fadeIn();
		
		// set key in case of valid insertion
		$form.on('done.yk.core.forms', function(event, data) {
            if ('valid' in data.data && data.data.valid) {
            	var $newForm = $(data.data.content).find('form[data-entry-key]'),
            		key = $newForm.attr('data-entry-key');
            	$form.attr('action', $newForm.attr('action'))
            		.attr('data-entry-key', key)
            		.data('entry-key', key);
            }
        });
		
		initRemoveButtons($content);
		
		// following lines should be solved via triggering global init event or something like that
		$content.find(".sf-container").summaryform();
		$content.find("a").click($.fn.summaryform.ensureSave);
		$content.find('form:not([data-handle-by]), form[data-handle-by="yk-form"]').form();
	};
	
	var removeButtonClickListener = function() 
	{
		var $button = $(this),
			$collectionContainer = $button.closest(selector);
		
		// confirm removal 
		if (!window.confirm($collectionContainer.data('remove-question'))) {
			return false;
		}
		
		var $formContainer = $button.closest('.form-collection-container-form'),
			key = $formContainer.find('form[data-entry-key]').data('entry-key'),
			action = $collectionContainer.data('remove-action'),
			remove = function () {
				$formContainer.fadeOut(function() {
					$formContainer.remove()
				});
			};
		
		// check for new entry form
		if (key === $collectionContainer.data('new-entry-key')) {
			// simply remove it from DOM
			remove();
		} else {
			$.ajax({
				url: '?action=' + action,
				type: 'post',
				dataType: 'json',
				data: {key: key}
			})
			.done(function(data) {
				if (data.success) {
					// remove it from DOM after success removal on server side
					remove();
				}
			});
		}
	};
	
	var initAddButtons = function (collection) {
		collection.find('.form-collection-container-add-button')
			.on('click.formcollectionContainer', addButtonClickListener);
	};
	
	var initRemoveButtons = function (collection) {
		collection.find('.form-collection-container-remove-button')
			.on('click.formcollectionContainer', removeButtonClickListener);
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