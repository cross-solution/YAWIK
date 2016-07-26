/**
 * YAWIK
 *
 * License: MIT
 * (c) 2013 - 2015 CROSS Solution <http://cross-solution.de>
 */

/**
 * Handles the "Track applications" form (\Jobs\Form\AtsMode)
 *
 * Author: Mathias Gelhausen <gelhausen@cross-solution.de>
 * Since: 0.19
 */
;
(function ($) {

	var $form;
	var visibilityMap = {
    	'intern': ['oneClickApply', 'oneClickApplyProfiles'],
		'uri': ['uri'],
    	'email': ['email']
    };
	
    function toggleAdditionalInputs(value)
    {
        var show = [];

		for (var type in visibilityMap) {
            var elements = visibilityMap[type];
            for (var key in elements) {
            	var $formGroup = $form.find('#atsMode-' + elements[key])
            		.closest('.form-group')
            		.slideUp();
            	if (type == value) {
            		show.push($formGroup)
            	}
    		}
        }
        
        for (var key in show) {
        	show[key].slideDown();
		}
    }

    function reloadIframe()
    {
        $('iframe')[0].contentDocument.location.reload(true);
    }

    $(function() {
        $form   = $('#description-descriptionForm-atsMode');
        var $select = $form.find('#atsMode-mode');

        $select.change(function () {
        	toggleAdditionalInputs($select.val());
        });
        $form.on('done.yk.core.forms', reloadIframe);
        toggleAdditionalInputs($select.val());
    })

})(jQuery); 
 
