
$(function () {
	$('#application-form').initform();
	
    $("#application-form").submit(function() {
    	var $this = $(this);
    	$this.find('*').inlineLabel('clear');
    	
    	
    	$.post(
    		'/' + lang + '/apply/submit',
    		$this.serialize(),
    		function(data) {
    			$this.find(':text, [type="email"], [type="date"], textarea').inlineLabel();
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