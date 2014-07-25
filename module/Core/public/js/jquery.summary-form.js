
;(function ($) {
	
	function Container($container)
	{
		this._init($container);
	};
	
	$.extend(Container.prototype, {
		
		submit: function(e, args)
		{
			var _this = this;
			var result = args.data;
			
			if ($.fn.spinnerbutton) {
				_this.$formContainer.find('button.sf-submit').spinnerbutton('toggle');
			}	
			if (result.valid) {
				_this.$summaryContainer.html(result.content)
				                       .find('.sf-edit').click($.proxy(_this.edit, _this));
				_this.cancel();
			}
			
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
			
			this.$formContainer.find('form').on('yk.forms.done', $.proxy(this.submit, this));
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