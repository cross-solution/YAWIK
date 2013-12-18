<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Applications\Form;
use Settings\Form\FormAbstract;

class Settings extends FormAbstract {
	
    public function getCoreFieldset() {
        return 'settings-applications-fieldset';
    }
    
}
