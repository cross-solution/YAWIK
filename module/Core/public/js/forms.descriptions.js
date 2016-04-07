
;(function($) {
	
	function Container($container)
	{
		this.$container    = $container;
		this.$descDiv      = $container.find('.daf-desc');
		this.$current      = this.$descDiv.find('.daf-desc-content');
		this.focus         = false; 
		this.blurTimeout   = null;
		this._init();
	}
	
	$.extend(Container.prototype, {
		
		_init: function()
		{
			var $form = this.$container.find('form');
			var _this = this;

            $form.find('.cam-description').hide().appendTo(this.$descDiv);
            $form.find(':input:not([id^="s2id_"]):not(select), [data-toggle="description"], .cam-description-toggle, .cam-description-toggle *')
			     .on('mouseover mouseout', $.proxy(this.eventToggle, this))
			     .focus($.proxy(function(event) {
//                    console.debug('focus');
			    	 if (this.blurTimeout) {
			    		 var $desc = this._getDescription($(event.target).attr('id'));
			    		 if ($desc) {
			    			 clearTimeout(this.blurTimeout);
			    			 this.blurTimeout = null;
			    		 }
			    	 }
			    	 this.eventToggle(event);
			     }, this))
			     .blur($.proxy(function(event) {
			    	 this.blurTimeout = setTimeout($.proxy(function() {
			    		 _this.eventToggle(event);
			    	 }, this), 200);
			     }, this));

            $form.find('select, .select2-container').on('focus', function(e) { console.debug(e); });
            $form.find('.select2-container').on('mouseover mouseout', function(event){
                var $select = $(event.currentTarget).parent().find('select');

                if (event.type.match(/mouse/)) {
                    if (true !== $select.data('focused')) {
                        var id = "mouseover"==event.type ? $select.attr('id') : null;
                        _this.toggle(id);

                    }

                }
            });

            $form.find('select').on( 'select2:open select2:close', function(event) {

                var $select = $(event.currentTarget);

                if ('select2:open' == event.type) {
                    $select.data('focused', true);
                } else {
                    $select.data('focused', false);
                    _this.toggle();
                }




            });
            $form.find('label').on("mouseover mouseout", function(event) {
                if ($(event.target).is('label')) {
                    var id = "mouseover" == event.type ? $(event.target).attr('for') : null;
                    _this.toggle(id);
                }
            });


		},
		
		_getDescription: function(id)
		{
            id = !id ? "__initial__" : id.replace(/^s2id_/, '');
            id = "__initial__" == id ? '.daf-desc-content' : '#' + id + '-desc';

            var $desc = this.$descDiv.find(id);
			
			if ($desc.length) {
				return $desc;
			}
			return false;
		},
		
		toggle: function(id, focus) 
		{
            //console.debug('toggle description', id);
			if (!id) {
				id = this.focus || '__initial__';
			}

			var $target = this._getDescription(id);
			
			if (!$target) {
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
				var $element = $(event.currentTarget);
                var id = $element.data('target');
                if (!id) {
                    id = $element.attr('id');
                }
				if ('blur' == event.type) {
					id = '__initial__';
				}
			}


			if (event.type.match(/mouse/) || !id) {
				this.toggle(id);
			} else {
				if ('focus' === event.type) {
                    if (id.match(/^s2id_/)) {
                        id = $element.parent().attr('id');
                    }
					this.focus = id;
				} else {
					this.focus = false;
				}
				
				this.toggle(id, 'focus' == event.type);
			}
		},

        select2Toggle: function(event)
        {
            console.debug(event);

            var $select  = $(event.target);
            var id       = $select.attr('id');
            var $select2 = $('#s2id_' + id);

            if (event.type.match(/focus/)) {
                this.focus = id;
                $select2.addClass('select2-container-active');
                console.debug($select);
            } else {
                this.focus = false;
                id         = "__initial__";
                $select2.removeClass('select2-container-active');
            }

            this.toggle(id);
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