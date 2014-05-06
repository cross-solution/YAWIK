<?php

namespace Applications\Form\Element;

use Core\Form\Element\PolicyCheck;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class PrivacyPolicy extends PolicyCheck
{    
        public function init()
    {
        $translator = $this->getTranslator();
        $uniq = md5(uniqid( '' , true ));
        $modalboxId = 'responsive-' . $uniq;
        $this->setName('privacypolicies')
             ->setLabel($translator->translate('Privacy Policies'))
             ->setAttribute('title', 'Privacy Policy')
             ->setAttribute('comment', sprintf($translator->translate("I have read the %s and accept it"), '<a href="disclaimer" data-target="#' . $modalboxId . '" data-toggle="modal">'.$translator->translate('Privacy Policy').'</a>'))
             ->setAttribute('modalboxid', $modalboxId)
                  ;
                     
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		           'name' => 'privacyPolicyAccepted',
                           ));
          
          
    }
    
    public function getInputFilterSpecification()
    {
        return array(
                'privacyPolicyAccepted' => array(
                        'required' => true,
                        'filters'  => array(
                                array('name' => '\Zend\Filter\StringTrim'),
                        ),
                        'validators' => array(
                                 array('name' => 'NotEmpty',
                                                 'options' => array(
                                                              'zero',
                                                              'messages' => array('isEmpty' => /* @translate */ 'please accept the privacy policy')
                                        )
                                )
                        ),
                ),
        );
    
    }
}