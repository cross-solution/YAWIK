
;(function ($) {
	
	function Container($container)
	{
		this._init($container);
	};
	
	$.extend(Container.prototype, {
		submit: function(event)
		{
			var _this = this;
			var $form = this.$formContainer.find('form');
			$.post(
				$form.attr('action'),
				$form.serializeArray(),
				null,
				'json'
			  )
			 .done(function(result) {
			
				if (result.ok) {
					_this.$summaryContainer.html(result.content);
					_this.cancel();
				} else {
					alert('Save failed.');
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
	
	$.fn.summaryform.data = {};
	
	$(function() { $(".sf-container").summaryform(); 
	$('#test-file-select').click(function() { $('#info-image').click(); })
		.on('drop', function(e) { console.debug(e); e.preventDefault(); })
		.on('dragenter, dragover', function(e) { e.preventDefault(); e.stopPropagation(); });
	});
	
})(jQuery);