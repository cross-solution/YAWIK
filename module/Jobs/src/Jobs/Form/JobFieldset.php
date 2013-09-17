<?php

namespace Jobs\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;
use Zend\Validator\StringLength as StringLengthValidator;
use Zend\Validator\EmailAddress as EmailAddressValidator;
use Zend\Validator\ValidatorInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class JobFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function getInputFilterSpecification()
    { 
        return array(
            'company' => array(
                'filters'  => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    new StringLengthValidator(1),
                ),
            ),
            'title' => array(
                'filters'  => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    new StringLengthValidator(5),
                ),
            ),
            'link' => array(
                'filters'  => array(
                    array('name' => 'Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                    new StringLengthValidator(5),
                ),
            ),
            'datePublishStart' => array(
                )
        );
    }
    
    public function init()
    {
        $this->setName('job');
        $this->setAttribute('id', 'job');
        $this->add(array(
            'type' => 'hidden',
            'name' => 'id'
        ));
        
       $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'applyId',
            'options' => array(
                'label' => 'applyId'
            ),
            'attributes' => array(
            //'id' => 'contact-title',
            )
        ));
        
       $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'company',
            'options' => array(
                'label' => 'company'
            ),
            'attributes' => array(
            //'id' => 'contact-title',
            ),
           
        ));
        
       $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'title',
            'options' => array(
                'label' => 'title'
            ),
            'attributes' => array(
            //'id' => 'contact-title',
            )
        ));
       
       $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'link',
            'options' => array(
                'label' => 'link'
            ),
            'attributes' => array(
            )
        ));
       
       $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'datePublishStart',
            'options' => array(
                'label' => 'datePublishStart'
            ),
            'attributes' => array(
            )
        ));
    }
}