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


class PrivacyFieldset extends Fieldset implements ViewPartialProviderInterface
{
    
    protected $viewPartial = 'form/core/privacy';
    
    public function init()
    {
        $this->setName('privacypolicies')
             ->setLabel('Privacy Policies');
                     
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
                                new \Zend\Validator\NotEmpty(),
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

