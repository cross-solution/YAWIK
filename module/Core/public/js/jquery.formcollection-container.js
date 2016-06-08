

(function($) {
	
	var selector = '.form-collection-container';
	
	var addButtonClickListener = function() {
		var $button = $(this),
			$collectionContainer = $button.closest(selector),
			$notSaved = $collectionContainer.find('.form-collection-container-form[data-entry-key=""]');
		
		// check for not saved forms
		if ($notSaved.length > 0) {
			$notSaved.find('form').find(':input').eq(0).focus();
			// disallow adding multiple forms at once (due to collection re-indexing)
			return;
		}
		
		var template = $collectionContainer.children('span[data-template]').data('template'),
			placeholder = $collectionContainer.data('template-placeholder'),
			key = $collectionContainer.find('.form-collection-container-form').length,
			$content = $(template.replace(new RegExp(placeholder, 'g'), key)),
			$form = $content.find('form'),
			$addWrapper = $collectionContainer.find('.form-collection-container-add-wrapper');
		
		// add new form before add button
		$content.insertBefore($addWrapper)
			.hide()
			.fadeIn();
		
		initRemoveButtons($content);
		
		$form.on('done.yk.core.forms', function(event, data) {
            if ('valid' in data.data && data.data.valid) {
            	// mark form saved
                $form.closest('.form-collection-container-form')
                	.data('entry-key', key)
                	.attr('data-entry-key', key);
            }
        });
		
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
			key = $formContainer.data('entry-key'),
			action = $collectionContainer.data('remove-action'),
			remove = function (exists) {
				$formContainer.fadeOut(function() {
					$formContainer.remove()
					if (exists) {
						reindex($collectionContainer);
					}
				});
			};
		
		// check if form is already saved
		if (key !== '') {
			$.ajax({
				url: '?action=' + action,
				type: 'post',
				dataType: 'json',
				data: {key: key}
			})
			.done(function(data) {
				if (data.success) {
					remove(true);
				}
			});
		} else {
			remove();
		}
	};
	
	var initAddButtons = function (collection) {
		collection.find('.form-collection-container-add-button').on('click.formcollectionContainer', addButtonClickListener);
	};
	
	var initRemoveButtons = function (collection) {
		collection.find('.form-collection-container-remove-button').on('click.formcollectionContainer', removeButtonClickListener);
	};
	
	var reindex = function (collection) {
		var pattern = collection.data('action-pattern'),
			placeholder = collection.data('template-placeholder'),
			regPattern = new RegExp(pattern.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1").replace(placeholder, '\\d+'));
		collection.find('.form-collection-container-form')
			.each(function (index) {
				var $formContainer = $(this),
				$form = $formContainer.find('form');
				$form.attr('action', $form.attr('action').replace(regPattern, pattern.replace(placeholder, index)));
			})
			.filter(function () {
				return $(this).data('entry-key') !== '';
			}).each(function (index) {
				var $formContainer = $(this);
				$formContainer.data('entry-key', index);
			});
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