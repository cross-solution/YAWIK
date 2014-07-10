
;(function ($) {
	
	function Container($container)
	{
		this._init($container);
	};
	
	$.extend(Container.prototype, {
		
		_displayErrors: function(errors, prefix)
		{
			var _this = this;
			$.each(errors, function(idx, error) {
				var $errorsDiv = _this.$formContainer.find('#' + prefix + idx + '-errors'); 
				if ($errorsDiv.length) {
					var html = '<ul class="error">'
					$.each(error, function(i, err) {
						html += '<li>' + err + '</li>';
					});
					html += '</ul>';
					$errorsDiv.html(html);
					$errorsDiv.parent().addClass('input-error');
				} else {
					_this._displayErrors(error, idx + '-');
				}
			});
		},
		
		_clearErrors: function()
		{
			this.$formContainer.find('.errors').each(function() {
				$(this).html('');
				$(this).parent().removeClass('input-error');
			});
		},
		
		submit: function(event)
		{
			var _this = this;
			var $form = this.$formContainer.find('form');
			this._clearErrors();
			$.post(
				$form.attr('action'),
				$form.serializeArray(),
				null,
				'json'
			  )
			 .done(function(result) {
				if ($.fn.spinnerbutton) {
					_this.$formContainer.find('button.sf-submit').spinnerbutton('toggle');
				}
				if (result.valid) {
					_this.$summaryContainer.html(result.content);
					_this.cancel();
				} else {
					_this._displayErrors(result.errors);
				}
			  })
			 .fail(function() {
				if ($.fn.spinnerbutton) {
					_this.$formContainer.find('button.sf-submit').spinnerbutton('toggle');
				}
			});
			
			return false;
		},
		
		cancel: function(event)
		{
			this.$formContainer.animate({opacity: 0, height: 'toggle'});
			this.$summaryContainer.animate({opacity:1, height: 'toggle'});
		},
		
		edit: function(event)
		{
			this.$formContainer.animate({opacity:1, height: 'toggle'});
			this.$summaryContainer.animate({opacity: 0, height: 'toggle'});
		},
		
		_init: function($container)
		{
			this.$mainContainer = $container;
			this.$formContainer = $container.find('.sf-form');
			this.$summaryContainer = $container.find('.sf-summary');
			
			this.displayMode = 'form';
			if ($container.data('display-mode')) {
				this.displayMode = $container.data('display-mode');
			}
			
			if ('summary' == this.displayMode) {
				this.$formContainer.hide().css('opacity', 0);
			} else {
				this.$summaryContainer.hide().css('opacity', 0);
			}
			
			this.$formContainer.find('.sf-submit').click($.proxy(this.submit, this));
			this.$formContainer.find('.sf-cancel').click($.proxy(this.cancel, this));
			this.$summaryContainer.find('.sf-edit').click($.proxy(this.edit, this));
		}
	});
	
	$.fn.summaryform = function ()
	{
		var containers = {}
		return this.each(function () {
			var $div = $(this);
			new Container($div);
			
		});
	};
	
	$(function() { $(".sf-container").summaryform();  });
	
})(jQuery);