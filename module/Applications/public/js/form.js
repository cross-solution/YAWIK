
$(function () {
	$("fieldset > legend").click(function() {
		var arrow = $(this).find('span');
		var currentClass = arrow.attr('class').replace(/^.*(ui-icon-[^\s]+).*$/, '$1');
		var newClass = currentClass.match(/n$/) 
					 ? currentClass.replace(/n$/, 's') 
				     : currentClass.replace(/.$/, 'n');
					 
		arrow.removeClass(currentClass).addClass(newClass);
		
		$(this).parent().find(".fieldset-content").slideToggle();
    });
        
    $(":text, [type='email']").inlineLabel();
        
    $("input[type=\'submit\'], button").button();
    
    $("#contact-title-wrapper")
    	.buttonset()
    	.attr('title', $("#contact-title-wrapper div.label").html())
    	.tooltip();
    
    
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