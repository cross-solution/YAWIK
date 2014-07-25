/**
 * YAWIK
 * jquery plugin to handle the main javascript of the application page.
 * At the moment it only copes the enabling and disabling of the "send application"
 * button.
 * 
 * @copyright 2013-2014 Cross Solution (http://cross-solution.de)
 * @license AGPLv3
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
;(function($, window) {
	
	var previewWindow;
	
	function doAction(action)
	{
		window.location.href = "?do=" + action;
	};
	
	function openPreview(e)
	{
		$button = $(e.currentTarget);
		
		if (previewWindow && !previewWindow.closed) {
			previewWindow.close();
		}
		
		previewWindow = window.open(
				$button.attr('href'), 'yawik-preview', 
				'dependent=yes,location=no,menubar=no,toolbar=no,scrollbars=yes'
		);
		previewWindow.focus();
		return false;
	};
	
	$(function() {
		var $sendButton = $('#send-application');
		var $abortButton= $('#abort-application');
		var $previewButton = $('#preview-application');
		
		$sendButton.click(function() { doAction('send'); });
		$abortButton.click(function() { doAction('abort'); });
		$previewButton.click(openPreview);
		
		$('form').on('yk.forms.done', function(e, result) {
			if (result.data.isApplicationValid) {
				$sendButton.removeClass('disabled');
				
			} else if (!$sendButton.hasClass('disabled')) {
				$sendButton.addClass('disabled');
			}
		});
	})
	
})(jQuery, window);
