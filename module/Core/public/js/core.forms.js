/**
 * implements the AJAX for forms
 *
 * all forms with
 *
 *
 */
;(function($) {
	
	var methods = {
			
			clearErrors: function($form)
			{
				$form.find('.errors').each(function() {
					$(this).html('');
					$(this).parent().removeClass('input-error');
				});
			},
			
			displayErrors: function($form, errors, prefix)
			{
				if (typeof errors === 'string') {
					return;
				}
                if (prefix == undefined) {
                    prefix = '';
                }
                $.each(errors, function(idx, error) {
                    var $errorsDiv = $form.find('#' + prefix + idx + '-errors');
//                    console.debug('inserting error messages', '#' + prefix + idx + '-errors', $errorsDiv, error);
                    if ($errorsDiv.length) {
                        var html = '<ul class="error">'
                        $.each(error, function(i, err) {
                            html += '<li>' + err + '</li>';
                        });
                        html += '</ul>';
                        $errorsDiv.html(html);
                        $errorsDiv.parent().addClass('input-error');
                    } else {
                        methods.displayErrors($form, error, prefix + idx + '-');
                    }
                });
			}
	};
	
	var handlers = {
		
		onSubmit: function(e, extraData) {
			var $form = $(e.currentTarget);
            var $button = $form.find('[type="submit"]');
            $button.attr('disabled', true);
			var data  = $form.serializeArray();
//            console.debug('data', e, $form, data);
			if (extraData) {
				$.each(extraData, function(idx, value) {
					data.push({
						name: idx,
						value: value
					});
				});
			}
			
			var dataType = $form.data('type');
			if (!dataType) dataType = 'json';

            $form.trigger('yk.forms.start', {data: data}); // DEPRECATED EVENT USE NEXT
            $form.trigger('start.yk.core.forms', {data: data}); //DEPRECATED EVENT USE NEXT

			var startEvent = $.Event('yk:forms:start');
            $form.trigger(startEvent, {data:data});

            if (!startEvent.preventSubmit) {
				$.ajax({
					url: $form.attr('action'),
					type: $form.attr('method'),
					dataType: dataType,
					data: data
				})
				.done(function(data, textStatus, jqXHR) {
					// the data-object can contains following values
					// valid = boolean ,if explicitly set to false, errors will be displayed
					methods.clearErrors($form);
					if ('valid' in data && !data.valid) {
						methods.displayErrors($form, data.errors);
					}
	//                console.debug('bubble done event for form',$form,data);
					$form.trigger('yk.forms.done', {data: data, status:textStatus, jqXHR:jqXHR}); // DEPRECATED EVENT USE NEXT
					$form.trigger('done.yk.core.forms', {data: data, status:textStatus, jqXHR: jqXHR}); //DEPRECATED EVENT USE NEXT
					$form.trigger('yk:forms:success', {data: data, status:textStatus, jqXHR: jqXHR});
					$form.trigger('ajax.ready', {'data': data});
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
						console.debug(textStatus, errorThrown);
					$form.trigger('yk.forms.fail', {jqXHR: jqXHR, status: textStatus, error: errorThrown}); // DEPRECATED EVENT USE NEXT
					$form.trigger('fail.yk.core.forms', {jqXHR: jqXHR, status: textStatus, error: errorThrown}); // DEPRECATED EVENT USE NEXT
					$form.trigger('yk:forms:fail', {jqXHR: jqXHR, status: textStatus, error: errorThrown});
				})
					.always(function() { $button.attr('disabled', false); });
			}
			return false;
		},
		
		onChange: function(e) {
			
			var $element = $(e.currentTarget);
			var validate = $element.data('validate');
			var data = {};
			if (validate) {
				data.validationGroup = validate;
			}
//            console.debug('triggering a submit on change', data);
			$element.parents('form').trigger('submit', data);
			return false;
		}
	};
	
	var helpers = {
		
		initSelect: function()
		{
			var $select = $(this);
            var data    = $select.data();
			var options = {theme:"bootstrap", width: 'resolve'};

            // allow disabling this autoinit routine.
            // Select2 elements must then be initialized explicitely.
            if (false == data.autoinit) {
                return;
            }

			$.each($select.data(), function(idx, val) {

				switch (idx) {
					case "allowclear":
						idx = "allowClear";
						val = '1' == val || 'true' == val;
						break;
                    case "searchbox":
                        idx = "minimumResultsForSearch";
                        val = false === val ? Infinity : parseInt(val);
                        break;
                    case "width":
                        idx = "width";
                        break;
					default:
						break;
				}

				options[idx] = val;
			});
			console.debug($select, options);
			$select.select2(options);
            $select.trigger('yk:forms:initSelect');
		},

        initWizardContainer: function()
        {
            var $container = $(this);

            $container
            .on('wizard:init.coreforms', function(e, $activeTab, $navigation, index) {
                var labels = [];
                $navigation.find('li a').each(function() { labels.push($(this).html());});
                $container.data('labels', labels);
            })
            .on('wizard:tabShow.coreforms', function(e, $tab, $navigation, index) {
                var labels = $container.data('labels');
                var $previous = $container.find('ul.wizard .previous a');
                var $next     = $container.find('ul.wizard .next a');


                if (0 === index) {
                    $previous.hide();
                } else {
                    $previous.show().html('&larr; ' + labels[index-1]);
                }

                if (labels.length !== index) {
                    $next.html(labels[index+1] + ' &rarr;');
                }
            });

            $container.bootstrapWizard({
                tabClass: 'nav nav-tabs nav-justified',
                onShow:           function() { $container.trigger('wizard:show', arguments) },
                onInit:           function() { $container.trigger('wizard:init', arguments) },
                onNext:           function() { $container.trigger('wizard:next', arguments) },
                onPrevious:       function() { $container.trigger('wizard:previous', arguments) },
                onLast:           function() { $container.trigger('wizard:last', arguments) },
                onFirst:          function() { $container.trigger('wizard:first', arguments) },
                onFinish:         function() { $container.trigger('wizard:finish', arguments) },
                onBack:           function() { $container.trigger('wizard:back', arguments) },
                onTabChange:      function() { $container.trigger('wizard:tabChange', arguments) },
                onTabClick:       function() { $container.trigger('wizard:tabClick', arguments) },
                onTabShow:        function() { $container.trigger('wizard:tabShow', arguments) }
            });
        }
	};

    /**
     * this function is called for all forms which has
     * - an attribute data-handle-by="yk-form"
     * - or has no attribute data-handle-by
     *
     * implement the triggers for an AJAX-Submit
     *
     * @param method
     * @returns
     */
	$.fn.form = function (method) 
	{
        var calledArguments = arguments;

		return this.each(function() {
			var $form = $(this);

            // enables the ability to call a distinct method for the picked forms,
            // has nothing to do with initiating the triggers below
			if (method && method in methods) {
				var args = [].slice.call(calledArguments, 1);
				return methods[method].apply(this, args);
			}

//            console.debug('ajax submit initialized for', $form);
            // overwrite the originally (HTML)-Submit for the form
			$form.submit(handlers.onSubmit);
            // triggers an ajax call for elements with this specific attribute 'data-trigger'
            // originally it is designed to immidiatly fire an submit event for input elements, after they have changed
			var elementsThatTriggerASubmit = $form.find('[data-trigger="submit"]');
//            console.debug('elements that trigger a submit',elementsThatTriggerASubmit);
            elementsThatTriggerASubmit.change(handlers.onChange);
		});
	};

    $.fn.form.initSelect = helpers.initSelect;

	if ($.fn.select2) {
		$.extend(
			$.fn.select2.defaults, 
			{
				minimumResultsForSearch: -1
			}
		);
	}
	
	$(function() {
		$('form:not([data-handle-by]), form[data-handle-by="yk-form"]').form();
		$('select').each(helpers.initSelect);
        $('.wizard-container').each(helpers.initWizardContainer);
	});

})(jQuery);
