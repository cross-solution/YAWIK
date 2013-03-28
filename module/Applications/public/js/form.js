
$(function () {
	$("fieldset > legend").click(function() {
		$("#" + $(this).parent().attr("id") + " .fieldset-content").slideToggle();
    });
        
    $(":text").inlineLabel();
        
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