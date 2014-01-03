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
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class PrivacyFieldset extends Fieldset implements ViewPartialProviderInterface, InputFilterProviderInterface
{
    
    protected $viewPartial = 'form/core/privacy';
    
    public function init()
    {
        $this->setName('privacypolicies')
             ->setLabel('Privacy Policies');
                     
          $this->add(array('type' => 'Zend\Form\Element\Checkbox',
        		           'name' => 'privacyPolicyAccepted',
                           'options' => array(
                                            //  'checked_value' => "1",
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
}

