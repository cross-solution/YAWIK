
;(function($) {
	
	var popup;
	
	var $currentFetchButton;
	
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
		var $buttons = $currentFetchButton.find('button');
		$.each(['btn-default', 'btn-success', 'btn-danger'], function(idx, className) {
			if ($buttons.hasClass(className)) {
				$buttons.removeClass(className);
			}
		});
		
		if ('' != data) {
			$buttons.addClass('btn-success');
			toggleDropdown($currentFetchButton, 'attach');
		} else {
			$buttons.addClass('btn-danger')
			toggleDropDown($currentFetchButton, 'detach');
		}
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
		$button.find('.spb-icon-normal').hide();
		$button.find('.spb-icon-processing').show();
		
		var url     = $button.data('fetch-url');
		
		popup = window.open(url, 'fetch social profile', 'width=380,height=450');
		$currentFetchButton = $button.parent();
		
		return false;
	};
	
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
				$('#' + $button.attr('id') + '-data').text('');
				var $buttons = $button.find('button');
				$.each(['btn-default', 'btn-success', 'btn-danger'], function(idx, className) {
					if ($buttons.hasClass(className)) {
						$buttons.removeClass(className);
					}
				});
				$buttons.addClass('btn-default');
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
			$attachActions.hide();
			$detachActions.show();
		} else {
			$attachActions.show();
			$detachActions.hide();
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
		toggleDropdown($buttons, 'detach');
	};
	
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