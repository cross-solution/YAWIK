<?php

namespace Applications\Form;
use Settings\Form\FormAbstract;

class Settings extends FormAbstract {
	
    public function getCoreFieldset() {
        return 'settings-applications-fieldset';
    }
    
}
