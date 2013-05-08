
$(function () {
	$("fieldset > legend").click(function() {
		var arrow = $(this).find('span');
		if (arrow.length) {
			var currentClass = arrow.attr('class').replace(/^.*(ui-icon-[^\s]+).*$/, '$1');
			var newClass = currentClass.match(/n$/) 
					     ? currentClass.replace(/n$/, 's') 
				         : currentClass.replace(/.$/, 'n');
					 
		    arrow.removeClass(currentClass).addClass(newClass);
		}
		
		$(this).parent().find(".fieldset-content").slideToggle();
    });
        
	
    $(":text, [type='email'], [type='date']").inlineLabel();
        
    $("input[type=\'submit\'], button").button();
    
    
    $("#application-title-wrapper")
    	.buttonset()
    	.attr('title', $("#application-title-wrapper label[for='application-title']").addClass('hidden').html())
    	.tooltip();
    
    
    $("#application-form [title]").tooltip('option', 'position', {
    	my: 'left',
    	at: 'right+10 center'
    	//of: $('#application')
    });
    console.debug(lang);
    $.datepicker.setDefaults($.datepicker.regional[lang]);
    $('[type="date"]').datepicker();
    
    $("#application-form").submit(function() {
    	$.post(
    		'/' + lang + '/apply/submit',
    		$(this).serialize(),
    		function(data) {
    			if (data.ok) {
    				$("#application-form").hide();
    				$("#application-saved-message").show();
    			} else {
    				core.displayFormErrors(data.messages);
    			}
    		}
    	);	
    	// prevent event default
    	return false;
    });
   
});