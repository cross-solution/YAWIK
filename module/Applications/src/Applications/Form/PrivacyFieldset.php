<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** AttachmentsFieldset.php */ 
namespace Applications\Form;

use Zend\Form\Fieldset;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ExplicitParameterProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class PrivacyFieldset extends Fieldset implements ViewPartialProviderInterface, InputFilterProviderInterface, ExplicitParameterProviderInterface
{    
    protected $viewPartial = 'form/core/privacy';
    
    public function init()
    {
        $uniq = md5(uniqid( '' , true ));
        $this->setName('privacypolicies')
             ->setLabel('Privacy Policies')
             ->setAttribute('title', 'Privacy Policy')
             //->setAttribute('comment', sprintf($this->translate("I have read the %s and accept it"), '<a href="disclaimer" data-target="#' . $modalboxid . '" data-toggle="modal">'.$this->translate('Privacy Policy').'</a>'))
             ->setAttribute('modalboxid', 'responsive-' . $uniq)
                  ;
                     
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		           'name' => 'privacyPolicyAccepted',
                           'attributes' => array(
                                            'title1' => 'anuisance'
                                             ),
                           'options' => array(
                                            //  'checked_value' => "1",
                                            'title2' => 'tilted and bailed'
                                             )
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
    
    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->viewPartial;
    }
    
    public function getParams()
    {
        return array(
            
        );
    }
}

