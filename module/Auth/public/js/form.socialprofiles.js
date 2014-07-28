
;(function($) {
	
	var popup;
	
	var $currentFetchButton;
	
	function _toggleButtonState($button, error)
	{
		var $buttons = $button.find('button');
		$.each(['btn-default', 'btn-success', 'btn-danger'], function(idx, className) {
			if ($buttons.hasClass(className)) {
				$buttons.removeClass(className);
			}
		});
		
		if (!error) {
			$buttons.addClass('btn-success');
			$button.data('is_attached', true);
			toggleDropdown($button, 'attach');
			$buttons[0].blur();
		} else {
			$buttons.addClass('btn-danger');
			toggleDropDown($button, 'detach');
		}
	}
	
	function fetchCompleted(event)
	{
		if (!popup || !$currentFetchButton) {
			return;
		}
		
		var data = popup.$('textarea').text();
		popup.close();
		popup = null;
		
		var id = '#' + $currentFetchButton.attr('id') + '-data';
		$(id).text(data);
		$form = $currentFetchButton.parents('form');

		$.post($form.attr('action'), $form.serialize());
		
		_toggleButtonState($currentFetchButton, '' == data);
		
		$currentFetchButton.find('.spb-icon-normal').show();
		$currentFetchButton.find('.spb-icon-processing').hide();
		$currentFetchButton = null;
		
		return false;
	}
	
	function buttonClicked(event)
	{
		if (popup) {
			if ($currentFetchButton && !popup.closed) {
				return false;
			}
			popup.close();
			popup = null;
		}
		
		var $button = $(event.currentTarget);
		
		if ($button.parent().data('is_attached')) {
			$button.blur();
			return false;
		}
		$button.find('.spb-icon-normal').hide();
		$button.find('.spb-icon-processing').show();
		
		var url     = $button.data('fetch-url');
		
		popup = window.open(url, 'fetch social profile', 'width=480,height=550');
		$currentFetchButton = $button.parent();
		
		return false;
	}
	
	function actionClicked(event)
	{
		var $link  = $(event.currentTarget);
		var action = $link.attr('href').substr(1);
		if (action.match(/\|/)) {
			var parts = action.split('|');
			action = parts[0];
			var actionData = parts[1];
		}
		var $button = $link.parent().parent().parent();
		
		switch (action) {
			case 'attach':
				$button.find('button:first-child').click();
				break;
			case 'detach':
				toggleDropdown($button, 'detach');
				$button.data('is_attached', false);
				$('#' + $button.attr('id') + '-data').text('');
				var $buttons = $button.find('button');
				$.each(['btn-default', 'btn-success', 'btn-danger'], function(idx, className) {
					if ($buttons.hasClass(className)) {
						$buttons.removeClass(className);
					}
				});
				$buttons.addClass('btn-default');
				var $form = $button.parent().parent().parent();
				$.post($form.attr('action'), $form.serialize());
				break;
			
			case 'view':
				var $modal = $('#' + $button.parent().parent().attr('id') + '-preview-box');
				var profileData = $('#' + $button.attr('id') + '-data').text();
				if (actionData !== undefined) {
					$modal.modal({
						remote: actionData,
						show: true,
						'usePost': true,
						'postData': {data: profileData}
					});
				} else {
					$modal.find('.modal-body').html('<pre style="max-height: 350px; overflow:auto">' +
						JSON.stringify(JSON.parse(profileData), null, " ")
						//.replace(/\n/, '<br>').replace(/\s/, '&nbsp;')
						+ '</pre>'
					);
					$modal.modal('show');
				}
				break;

		}
		
		$link.parent().parent().dropdown('toggle');
		return false;
	}
	
	function toggleDropdown($button, action)
	{
		var $attachActions = $button.find('.spb-action-attach');
		var $detachActions = $button.find('.spb-action-detach, .spb-action-preview');
		
		if ('attach' == action) {
			$attachActions.addClass('disabled');
			$detachActions.removeClass('disabled');
		} else {
			$attachActions.removeClass('disabled');
			$detachActions.addClass('disabled');
		}
	}
	
	function initFieldset($fieldset)
	{
		var $buttons = $fieldset.find('.social-profiles-button');
		$buttons.find('button:first-child')
		         .on('click.socialprofiles', buttonClicked);
		$buttons.find('.dropdown-menu a')
			    .on('click.socialprofiles', actionClicked);
		$buttons.find('.spb-icon-processing').hide();
		
		$buttons.each(function () {
			var $button = $(this);
			var $area = $('#' + $button.attr('id') + '-data');
			var data  = $area.val();
			if ('' != data) {
				_toggleButtonState($button, false);
				toggleDropdown($button, 'attach');
			} else {
				toggleDropdown($button, 'detach');
			}
		});
	}
	
	$.fn.socialprofiles = function()
	{
		return this.each(function() {
			initFieldset($(this));
		});
	};
	
	$(function() { 
		$(document).on('fetch_complete.socialprofiles', fetchCompleted);
		$('.social-profiles-fieldset').socialprofiles() 
	});
	
})(jQuery);