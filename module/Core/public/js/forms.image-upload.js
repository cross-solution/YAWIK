
;(function ($) {
	
	$(function() {
		$(document).on("drop dragover", function(e) { e.preventDefault(); e.stopPropagation(); });
		$('.single-file-upload .iu-dropzone').click(function(e) {
			var $target = $(e.target);
			if ('file' == $target.attr('type') || $target.hasClass('fu-delete-button') || $target.parents('a.fu-delete-button').length) {
				e.stopPropagation();
			} else {
				$(this).find('input').click();
				return false;
			}
		});
		
		$('.single-file-upload').each(function() {
			var $form = $(this);
			
			$form.find('.iu-delete-button').click(function(e) {
				$.get($form.find('img').attr('src') + '?do=delete')
				 .always(function() {
					 $form.find('img').attr('src', '').hide();
					$form.find('.iu-delete-button').hide();
				 });
			});
			if ($form.data('is-empty')) {
				$form.find('.iu-preview').hide();
				$form.find('.iu-delete-button').hide();
			}
			
			$form.fileupload({
				dataType: 'json',
				dropZone: $form.find('.iu-dropzone'),
				
				add: function(e, data)
				{
					$form.find('.iu-progress').show();
					data.submit();
				},
				
				progress: function(e, data)
				{
					var $form = $(data.form);
					
					$form.find('.iu-progress-percent').text(
						parseInt(data.loaded / data.total * 100, 10)
					);
				},
				
				done: function(e, data)
				{
					$form = $(data.form);
					
					if (!data.result.content) {
						$form.find('.iu-preview').html('').hide();
						$form.find('.iu-delete-button').hide();
					} else {
						var $content = $(data.result.content);
						var preview  = $content.find('.iu-preview').html();
						$form.find('.iu-preview').html(preview).show();
						$form.find('.iu-delete-button').show();
					}
					$form.find('.iu-progress').hide().find('.iu-progress-percent').text("0");
					$form.find('.iu-delete-button').show();
				},
				
				fail: function(e, data) 
				{
					console.debug(e, data);
				}
			});
		});
	});
	
})(jQuery);