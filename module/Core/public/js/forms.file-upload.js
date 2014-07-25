
;(function ($) {
	
	function formatFileSize(bytes) 
	 {
	        if (typeof bytes !== 'number') {
	            return '';
	        }

	        if (bytes >= 1000000000) {
	            return (bytes / 1000000000).toFixed(2) + ' GB';
	        }

	        if (bytes >= 1000000) {
	            return (bytes / 1000000).toFixed(2) + ' MB';
	        }

	        return (bytes / 1000).toFixed(2) + ' kB';
	};

	function deleteFile(event){
		$tpl = $(event.currentTarget).parent();
		if ($tpl.hasClass('fu-working')) {
			jqXHR.abort();
			$tpl.fadeOut(function() { $tpl.remove(); });
		} else {
			$.get($tpl.find('.fu-file-info').attr('href') + '?do=delete')
			 .always(function() {
				 $tpl.fadeOut(function() { $tpl.remove(); });
			 });
		}
		return false;
	};
	
	$(function() {
		//$(document).on("drop dragover", function(e) { e.preventDefault(); e.stopPropagation(); });
		$('.fu-dropzone').click(function(e) {
			if (1 == e.target.nodeType && 'A' != e.target.nodeName && 'INPUT' != e.target.nodeName) {
				$(this).find('input').click();
				return false;
			} else {
				e.stopPropagation();
			}
			
			//return false;
		});
		
		$('.multi-file-upload').each(function() {
			var $form = $(this);
			
			$form.find('.fu-remove-all').click(function() {
				$form.find('.fu-dropzone .fu-files .fu-delete-button').click();
				
				return false;
			});
			
			$form.find('.fu-file .fu-delete-button').click(deleteFile);
			$form.find('.fu-file .fu-progress').hide();
			
			$form.fileupload({
				dataType: 'json',
				dropZone: $form.find('.fu-dropzone'),
				
				add: function(e, data)
				{
					console.debug(e, data);
					var iconType = "fa-file";
					var fileType = data.files[0].type;
					
					if (fileType.match(/^image\//)) {
						iconType += '-image-o';
					} else {
						iconType += '-o';
					}
					
					var tpl = $form.find('.fu-template').data('template')
					               .replace(/__file-name__/, data.files[0].name)
					               .replace(/__file-size__/, formatFileSize(data.files[0].size))
					               .replace(/fa-file-o/, iconType);
					               
					
					var $tpl = $(tpl);
					console.debug($tpl, $form.find('.fu-files'));
					data.context = $tpl.appendTo($form.find('.fu-files'));
					
					$tpl.find('.fu-delete-button').click(deleteFile);
					
					var jqXHR = data.submit();
				},
				
				progress: function(e, data)
				{
					var $form = $(data.form);
					var progress = parseInt(data.loaded / data.total * 100, 10);
					
					$form.find('.fu-progress-text').text(progress);
				},
				
				done: function (e, data)
				{
					data.context.removeClass('fu-working');
					data.context.find('.fu-progress').addClass('hide');
					data.context.find('.fu-file-info').attr('href', data.result.content);
				},
				
				fail: function(e, data) 
				{
					console.debug(e, data);
				}
			});
		});
	});
	
})(jQuery);