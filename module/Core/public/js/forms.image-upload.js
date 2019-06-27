
;(function ($) {

	$(function() {
		$(document).on("drop dragover", function(e) { e.preventDefault(); e.stopPropagation(); });
		$('.single-file-upload .iu-dropzone').click(function(e) {
			var $target = $(e.target);
			if ('file' == $target.attr('type') || $target.hasClass('iu-delete-button') || $target.parents('a.iu-delete-button').length) {
				e.stopPropagation();
			} else {
				$(this).find('input').click();
				return false;
			}
		});

		$('.single-file-upload').each(function() {
			var $form = $(this);
			var $dropzone = $form.find('.iu-dropzone');

			if (0 == $dropzone.length) { return; }
			
			$form.find('.iu-delete-button').click(function(e) {
				$.get($form.find('img').attr('src') + '?do=delete')
				 .always(function() {
					// $form.find('img').attr('src', '').hide();
					$form.find('.iu-delete-button').hide();
					$form.find('.iu-preview').hide();
					$form.find('.iu-empty-notice').show();
				 });
			});
			if ($form.data('is-empty')) {
				$form.find('.iu-preview').hide();
				$form.find('.iu-delete-button').hide();
			} else {
				$form.find('.iu-empty-notice').hide();
			}

			$form.find('.iu-errors, .iu-errors li').hide();

			$form.fileupload({
				dataType: 'json',
				dropZone: $dropzone,

				add: function(e, data)
				{
					$form.find('.iu-errors, .iu-errors li').hide();
					$form.find('.iu-empty-notice').hide();
					$form.find('.iu-progress').show();

					var options = $dropzone.find('input[type="file"]').data();
					var hasErrors  = false;

					if (options.maxsize && options.maxsize < data.files[0].size) {
						hasErrors = true;
						$form.find('.iu-error-size').show();
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
							hasErrors = true;
							$form.find('.iu-error-type').show();
						}
					}

					if (hasErrors) {
						$form.find('.iu-errors').show();
						$form.find('.iu-progress').hide();
						$form.find('.iu-preview').hide();
						$form.find('.iu-empty-notice').show();
						return;
					}

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

                    if (!data.result.valid) {
                        for (errorKey in data.result.errors.image) {
                            switch (errorKey) {
                                case 'fileMimeTypeFalse':
                                    $form.find('.iu-error-type').show();
                                    break;

                                case 'fileSizeTooBig':
                                    $form.find('.iu-error-size').show();
                                    break;
                            }
                        }
                        $form.find('.iu-errors').show();
                        $form.find('.iu-progress').hide();
                        $form.find('.iu-preview').hide();
                        $form.find('.iu-empty-notice').show();
                        return;
                    }
					if (!data.result.content) {
						$form.find('.iu-preview').html('').hide();
						$form.find('.iu-empty-notice').show();
						$form.find('.iu-delete-button').hide();
					} else {
						var $content = $(data.result.content);
						var preview  = $content.find('.iu-preview').html();
						$form.find('.iu-preview').html(preview).show();
						$form.find('.iu-empty-notice').hide();
						$form.find('.iu-delete-button').show();
					}
					$form.find('.iu-progress').hide().find('.iu-progress-percent').text("0");
					$form.find('.iu-delete-button').show();
				},

				fail: function(e, data)
				{
					console.debug(e, data);
                    console.debug(data.files[0].error);
				}
			});
		});
	});

})(jQuery);
