<?php

namespace Auth\Form;
use Settings\Form\FormAbstract;

class Settings extends FormAbstract {
	
    public function getCoreFieldset() {
        return 'settings-auth-fieldset';
    }
    
}
