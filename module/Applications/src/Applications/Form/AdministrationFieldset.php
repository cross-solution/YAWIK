<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;

class AdministrationFieldset extends Fieldset
{
    
     
    public function init()
    {
        $this->setName('privacypolicies')
             ->setLabel('Privacy Policies');
                     
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		'name' => 'privacyPolicyAccepted',
        		'options' => array('label' => /* @translate */ '<button href="disclaimer" data-target="#responsive" data-toggle="modal">Privacy Policy</button>',
                            'description' => 
                                '<div id="responsive" class="modal fade"><div class="modal-dialog"><div class="modal-content">
                                    <div class="modal-body">Bitte warten ...
                                    </div>
                                </div></div></div>',
                            )));
    }
}

