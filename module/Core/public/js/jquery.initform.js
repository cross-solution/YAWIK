

(function($) {
	var initRadioButtonSet = function () {
        $this = $(this);
        var id = $this.attr('id').replace(/-wrapper$/, '');
        var title = $this.find('label[for="' + id + '"]').addClass('hidden').html();

        $this.buttonset();
        if (title) {
            $this.attr('title', title);
        }

    };
	
	var toggleFieldsetListener = function() 
	{
		var arrow = $(this).find('span');
		
		if (!arrow.length) { return false; }
		var currentClass = arrow.attr('class').replace(/^.*(ui-icon-[^\s]+).*$/, '$1');
		var newClass = currentClass.match(/s$/) 
				     ? currentClass.replace(/s$/, 'n') 
			         : currentClass.replace(/.$/, 's');
					 
		arrow.removeClass(currentClass).addClass(newClass);
		
		$(this).parent().find("> .fieldset-content").slideToggle();
		return false;
    };
	
	$.fn.initform = function( ) 
	{
		
		if (!$.fn.inlineLabel || !$.fn.daterange || !$.fn.formcollection || !$.fn.selectmenu) { 
			throw new ReferenceError('initform requires plugins inlineLabel, daterange, selectmenu and formcollection'); 
		}
		
		
		return this.each(function() 
		{
			var parent = $(this);
			parent.find(':text, [type="email"], [type="date"], textarea').inlineLabel();
			parent.find('select').selectmenu();
			parent.find('.form-element-radio').each(initRadioButtonSet);
			parent.find("[title]").tooltip();
		    parent.find("input[type=\'submit\'], input[type='checkbox'], button").button();
		    parent.find(".form-element-daterange").daterange();
		    $.datepicker.setDefaults($.datepicker.regional[lang]);
		    parent.find('[type="date"]').datepicker();
		    parent.find("fieldset > legend").click(toggleFieldsetListener);
		    parent.find(".form-collection").formcollection();
		});
	};
	
	
})(jQuery);