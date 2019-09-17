// TODO: documentation

;(function ($) {

    function getObjPath(obj, path)
    {
        if (obj.hasOwnProperty(path)) {
            return obj[path];
        }

        var result = obj, pathArray = path.split('.');
        $.each(pathArray, function(i, value) {
            if (!result.hasOwnProperty(value)) {
                result = {};
                return false;
            }

            result = result[value];
            return true;
        });

        return result;
    }

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
		var $tpl = $(event.currentTarget).parent();
		var $ul  = $tpl.parent();
		function removeLi($tpl) {
			$tpl.remove();
			if (!$ul.find('li').length) {
				$ul.parent().find('.fu-empty-notice').show();
				$ul.parent().find('.fu-nonempty-notice').hide();
			}
		};

		if ($tpl.hasClass('fu-error')) {
			$tpl.fadeOut(function() { removeLi($tpl); });
		} else 	if ($tpl.hasClass('fu-working')) {
			e.data.jqXHR.abort();
			$tpl.fadeOut(function() { removeLi($tpl); });
		} else {
			$.get($tpl.find('.fu-file-info').attr('href') + '?do=delete')
			 .always(function() {
				 $tpl.fadeOut(function() { removeLi($tpl); });
			 });
		}
		return false;
	};

	$(function() {
		//$(document).on("drop dragover", function(e) { e.preventDefault(); e.stopPropagation(); });
		$('.fu-dropzone').not('.fu-non-clickable').click(function(e) {
			var $target = $(e.target);
			if ('file' == $target.attr('type')
				|| $target.hasClass('fu-delete-button')
				|| $target.hasClass('fu-file-info')
				|| $target.parents('a.fu-delete-button, a.fu-file-info').length
			) {
				e.stopPropagation();
			} else {
				$(this).find('input').click();
				return false;
			}
		});

		$('.multi-file-upload, .single-file-upload').each(function() {
			var $form = $(this);

      if (0 === $form.find('.fu-dropzone').length) { return; }

			$form.find('.fu-remove-all').click(function() {
				$form.find('.fu-dropzone .fu-files .fu-delete-button').click();

				return false;
			});

			$form.find('.fu-file .fu-delete-button').click(deleteFile);
			$form.find('.fu-file .fu-progress, .fu-file .fu-errors').hide();

			$form.fileupload({
				dataType: 'json',
				dropZone: $form.find('.fu-dropzone'),

				add: function(e, data)
				{
					$form.find('.fu-empty-notice').hide();
					$form.find('.fu-nonempty-notice').show();

					var iconType = "fa-file";
					var fileType = data.files[0].type;

					if (fileType.match(/^image\//)) {
						iconType += '-image-o';
	 				} else if (fileType.match(/pdf$/)) {
						iconType += '-pdf-o';

					} else {
						iconType += '-o';
					}

					var tpl = $form.find('.fu-template').data('template')
					               .replace(/__file-name__/, data.files[0].name)
					               .replace(/__file-size__/, formatFileSize(data.files[0].size))
					               .replace(/fa-file-o/, iconType);

					var $tpl = $(tpl);

					var options = $form.find('.fu-dropzone input[type="file"]').data();

					var errors = {
						size: false,
						type: false
					};

					if (options.maxsize && data.files[0].size > options.maxsize) {
						errors.size = true;
					}
					if (options.allowedtypes) {
						var types = options.allowedtypes.split(',');
						var found = false;

						for (var i = 0; i < types.length; i++) {
							if (0 === data.files[0].type.indexOf(types[i])) {
								found = true;
								break;
							}
						}

						if (!found) {
							errors.type = true
						}
					}

					data.context = $tpl.appendTo($form.find('.fu-files'));

					if (errors.size || errors.type) {
						$tpl.removeClass('fu-working').addClass('fu-error')
						    .find('.fu-progress').hide();

                        $tpl.find('li').hide();
						if (errors.size) {
							$tpl.find('.fu-error-size').show();
						}
						if (errors.type) {
							$tpl.find('.fu-error-type').show();
						}
						$tpl.find('.fu-delete-button').click(deleteFile);
						return;
					}

					$tpl.find('.fu-errors, .fu-errors li').hide();

					var jqXHR = data.submit();
					$tpl.find('.fu-delete-button').click({jqXHR: jqXHR}, deleteFile);


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
                    if (data.result.valid) {
					    data.context.find('.fu-file-info').attr('href', data.result.content);
                        return;
                    }

                    var errorObjKey = $form.data('errorkey') || $form.attr('action').replace(/\?form=/, '');
                    var errors      = getObjPath(data.result.errors, errorObjKey);

                    data.context.removeClass('fu-working').addClass('fu-error')
                        .find('.fu-errors').show();
                    for (var errorKey in errors) {
                        switch (errorKey) {
                            case 'fileMimeTypeFalse':
                                data.context.find('.fu-error-type').show();
                                break;
                            case 'fileSizeTooBig':
                                data.context.find('.fu-error-size').show();
                                break;
                            case 'callbackValue':
                                data.context.find('.fu-error-count').show();
                                break;
                        }
                    }


				},

				fail: function(e, data)
				{
                    data.context.find('.fu-progress').addClass('hide');
                    data.context.removeClass('fu-working').addClass('fu-error')
                        .find('.fu-errors').show();
                    data.context.find('.fu-error-general').show();

					console.debug(e, data);
				}
			});
		});
	});

})(jQuery);
