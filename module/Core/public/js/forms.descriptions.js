
;(function($) {
	
	function Container($container)
	{
		this.$container    = $container;
		this.$descDiv      = $container.find('.daf-desc');
		this.$current      = this.$descDiv.find('.daf-desc-content');
		this.focus         = false; 
		this.blurTimeout   = null;
		this._init();
	};
	
	$.extend(Container.prototype, {
		
		_init: function()
		{
			var $form = this.$container.find('form');
			var _this = this;
			
			$form.find('.cam-description').hide().appendTo(this.$descDiv);
			$form.find(':input')
			     .on('mouseover mouseout', $.proxy(this.eventToggle, this))
			     .focus($.proxy(function(event) {
			    	 if (this.blurTimeout) {
			    		 var $elem = $(event.target);
			    		 if (this.$descDiv.find('#' + $(event.target).attr('id') + '-desc').length) {
			    			 clearTimeout(_this.blurTimeout);
			    			 this._blurTimeout = null;
			    		 }
			    	 }
			    	 this.eventToggle(event);
			     }, this))
			     .blur($.proxy(function(event) {
			    	 this.blurTimeout = setTimeout($.proxy(function() {
			    		 _this.eventToggle(event);
			    	 }, this), 200);
			     }, this));
			     
		},
		
		_getDescription: function(event)
		{
			var $element = $(event.target);
			var $desc    = this.$descDiv.find('#' + $element.attr('id') + '-desc');
			
			if ($desc.length) {
				return $desc;
			}
			return false;
		},
		
		toggle: function(id, focus) 
		{
			if (!id) {
				id = this.focus || '__initial__';
			}
			
			var target  = '__initial__' == id ? '.daf-desc-content' : '#' + id + '-desc';
			var $target = this.$descDiv.find(target);
			
			if (!$target.length) {
				return;
			}
			
			
			if (!$target.is(this.$current)) {
				this.$current.stop().animate({opacity: 0, height: 'toggle', marginTop: 'toggle', marginBottom: 'toggle', paddingTop: 'toggle', paddingBottom: 'toggle'});
				$target.stop().animate({opacity: 1, height: 'toggle', marginTop: 'toggle', marginBottom: 'toggle', paddingTop:'toggle', paddingBottom: 'toggle'});
			
				this.$current = $target;
			}
		},
		
		eventToggle: function(event)
		{
			if ('mouseout' == event.type) {
				var id = null;
			} else {
				var $element = $(event.target);
				var id       = $element.attr('id');
				if ('blur' == event.type) {
					id = '__initial__';
				}
			}
			
			if (event.type.match(/mouse/)) {
				this.toggle(id);
			} else {
				if ('focus' === event.type) {
					this.focus = id;
				} else {
					this.focus = false;
				}
				
				this.toggle(id, 'focus' == event.type);
			}
		},
		
		onMouseOver: function(event)
		{
			var $desc = this._retrieveDescription(event);
			if (!$desc) { return; }
			
			var $element = $(event.target);
			
		}
	});
	
	$.fn.formdesc = function()
	{
		return this.each(function() {
			new Container($(this));
		});
	};
	
	$(function() {
		$('.daf-form-container').formdesc();
	});
	
})(jQuery);