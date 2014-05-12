<?php

namespace Applications\Form\Element;

use Core\Form\Element\PolicyCheck;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class CarbonCopy extends PolicyCheck
{    
        public function init()
    {
        $translator = $this->getTranslator();
        $this->setName('carboncopy')
             ->setLabel($translator->translate('Carbon Copy'))
             ->setAttribute('title', 'Carbon Copy')
             ->setAttribute('comment', $translator->translate('send me a carbon copy of my application'))
             //->setAttribute('modalboxid', $modalboxId)
                  ;
                     
        $this->add(array('type' => 'Zend\Form\Element\Checkbox',
            'name' => 'carboncopy',
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