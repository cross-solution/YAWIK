/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

;(function($) {
	
	var $input;
	var $display;
	var state = 'input';
	
	function toggleInput()
	{
		if ('input' == state) {
			
			$input.hide();
			var $displayNode = $display.find('.apply-identifier-link');
			var displayHtml  = $displayNode.text();
			var content = displayHtml.replace(/[^\/]+$/, $input.find('input').val());
			console.debug($displayNode, displayHtml, content, $input.find('input').val());
			$displayNode.text(content);
			$display.show();
			state = 'display';
		} else {
			$input.show().find('input').focus();
			$display.hide();
			state = 'input';
		}
		return false;
	}
	
	function checkInput()
	{
		var applyId = $input.find('input').val();
		$.get(basePath + '/' + lang + '/jobs/check-apply-id', {applyId: applyId})
		 .done(function(data) {
			 if (data.ok) {
				 $input.find('.input-error').removeClass('input-error');
				 $input.find('#job-applyId-errors').html('').hide();
				 return toggleInput();
			 }
			 
			 $input.find('input, #job-applyId-span').addClass('input-error');
			 var errors = '<ul>';
			 $.each(data.messages, function(idx, item) { errors+='<li>' + item + '</li>'; });
			 errors += '</ul>';
			 $input.find('#job-applyId-errors').html(errors).show();
		 });
		 
		
		return false;
	}
	
	function init()
	{
		$input = $('.apply-identifier-wrapper .apply-identifier-input');
		$display = $('.apply-identifier-wrapper .apply-identifier-display');
		
		$input.find('input').keypress(function(e) { if (13 == e.which) { return checkInput(); }});
		$display.click(toggleInput)
            .removeClass('hidden').hide()
            .find('.panel').css('margin-bottom', '0px');
         
		if (!$input.find('#job-applyId-span').hasClass('input-error')) {
			$input.hide();
			$display.show();
			state="display";
		}
	}
	
	$(init);
	
})(jQuery);