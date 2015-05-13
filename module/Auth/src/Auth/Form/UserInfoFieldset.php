<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Fieldset;
use Core\Entity\RelationEntity;
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class UserInfoFieldset extends Fieldset implements ViewPartialProviderInterface,
                                                   InputFilterProviderInterface
{
    
    protected $viewPartial = 'form/auth/contact';

    /**
     * @param String $partial
     * @return $this
     */
    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
        return $this;
    }

    /**
     * @return string
     */
    public function getViewPartial()
    {
        return $this->viewPartial;
    }

    /**
     * @return \Zend\Stdlib\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
	public function init()
    {
        $this->setName('info');
             //->setLabel( /* @translate */ 'personal informations');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(array(
            'name' => 'email',
            'options' => array( 'label' => /* @translate */ 'Email' ),
         ));
               
        $this->add(array(
        		'name' => 'phone',
                'type' => '\Core\Form\Element\Phone',
        		'options' => array(
        				'label' => /* @translate */ 'Phone',
        		),
                'maxlength' => 20,
        ));
        
        $this->add(array(
                'type' => 'StrippedTagText',
        		'name' => 'postalcode',
        		'options' => array(
        				'label' => /* @translate */ 'Postalcode'
        		)
        ));
        
        $this->add(array(
                'type' => 'StrippedTagText',
        		'name' => 'city',
        		'options' => array(
        				'label' => /* @translate */ 'City'
        		)
        ));
        
        $this->add(array(
        		'name' => 'gender',
        		'type' => 'Zend\Form\Element\Select',
        		'options' => array(
        				'label' => /*@translate */ 'Salutation',
        				'value_options' => array(
        						'' => '', // => /*@translate */ 'please select',
        						'male' => /*@translate */ 'Mr.',
        						'female' => /*@translate */ 'Mrs.',
        				)
        		),
                'attributes' => array(
                    'data-placeholder' => /*@translate*/ 'please select',
                    'data-allowclear' => 'true',
                ),
        ));
        
        $this->add(array(
            'name' => 'firstName',
            'options' => array(
                'label' => /*@translate*/ 'First name',
                'maxlength' => 50,
            ),
        ));
        
        $this->add(array(
            'type' => 'StrippedTagText',
            'name' => 'lastName',
            'options' => array(
                'label' => /*@translate*/ 'Last name',
                'maxlength' => 50,    
            ),
            'required' => true
        ));
        
        $this->add(array(
                'type' => 'StrippedTagText',
        		'name' => 'street',
        		'options' => array(
        				'label' => /*@translate*/ 'street'
        		)
        ));
        
        $this->add(array(
                'type' => 'StrippedTagText',
        		'name' => 'houseNumber',
        		'options' => array(
        				'label' => /*@translate*/ 'house number'
        		)
        ));
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getInputFilterSpecification()
    {
        return array(
            'firstName' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => '\Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                            new \Zend\Validator\NotEmpty(),
                            new \Zend\Validator\StringLength(array('max' => 50))
                ),
            ),
            'lastName' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    new \Zend\Validator\NotEmpty(),
                    new \Zend\Validator\StringLength(array('max' => 50))
                ),
            ),
            'email' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                        new \Zend\Validator\NotEmpty(),
                        new \Zend\Validator\StringLength(array('max' => 100)),
                        new \Zend\Validator\EmailAddress()
                )
            ),
            'image' => array(
                'required' => false,
                'filters'  => array(
                ),
                'validators' => array(
                    new \Zend\Validator\File\Exists(),
                    new \Zend\Validator\File\Extension(array('extension' => array('jpg', 'png', 'jpeg', 'gif'))),
                ),
            ),
        );
    }
}