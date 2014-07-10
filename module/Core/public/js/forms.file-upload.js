
;(function ($) {
	
	$(function() {
		$(document).on("drop dragover", function(e) { e.preventDefault(); e.stopPropagation(); });
		$('.single-file-upload .fu-dropzone').click(function(e) {
			if (1 == e.target.nodeType && 'DIV' == e.target.nodeName) {
				$(this).find('input').click();
				return false;
			} else {
				e.stopPropagation();
			}
			
			//return false;
		});
		
		$('.single-file-upload').each(function() {
			var $form = $(this);
			
			$form.find('.fu-delete-button').click(function(e) {
				$.get($form.find('img').attr('src') + '?do=delete')
				 .always(function() {
					 $form.find('img').attr('src', '').hide();
					$form.find('.fu-delete-button').hide();
				 });
			});
			if ($form.data('is-empty')) {
				$form.find('.fu-preview').hide();
				$form.find('.fu-delete-button').hide();
			}
			
			$form.fileupload({
				dataType: 'json',
				dropZone: $form.find('.fu-dropzone'),
				
				add: function(e, data)
				{
					$form.find('.fu-progress').show();
					data.submit();
				},
				
				progress: function(e, data)
				{
					var $form = $(data.form);
					
					$form.find('.fu-progress-percent').text(
						parseInt(data.loaded / data.total * 100, 10)
					);
				},
				
				done: function(e, data)
				{
					$form = $(data.form);
					
					if (!data.result.content) {
						$form.find('.fu-preview').html('').hide();
						$form.find('.fu-delete-button').hide();
					} else {
						var $content = $(data.result.content);
						var preview  = $content.find('.fu-preview').html();
						$form.find('.fu-preview').html(preview).show();
						$form.find('.fu-delete-button').show();
					}
					$form.find('.fu-progress').hide().find('.fu-progress-percent').text("0");
					$form.find('.fu-delete-button').show();
				},
				
				fail: function(e, data) 
				{
					console.debug(e, data);
				}
			});
		});
	});
	
})(jQuery);