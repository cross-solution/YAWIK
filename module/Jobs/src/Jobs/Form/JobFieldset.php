<?php

namespace Jobs\Form;

use Zend\Form\Fieldset;
use Zend\Validator\StringLength as StringLengthValidator;
use Zend\Validator\EmailAddress as EmailAddressValidator;
use Zend\Validator\ValidatorInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Core\Repository\Hydrator;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\ArrayToCollectionStrategy;

class JobFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new EntityHydrator();
            /*
            $datetimeStrategy = new Hydrator\DatetimeStrategy();
            $datetimeStrategy->setHydrateFormat(Hydrator\DatetimeStrategy::FORMAT_MYSQLDATE);
            $hydrator->addStrategy('datePublishStart', $datetimeStrategy);
             */
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setAttribute('id', 'job-fieldset');
        $this->setLabel('Job details');

        $this->add(array(
            'type' => 'hidden',
            'name' => 'id'
        ));
        
       $this->add(array(
            'type' => 'Text',
            'name' => 'applyId',
            'options' => array(
                'label' => /*@translate*/ 'Apply Identifier'
            ),
        ));
        
       $this->add(array(
            'type' => 'Text',
            'name' => 'company',
            'options' => array(
                'label' => /*@translate*/ 'Company'
            ),
        ));
        
       $this->add(array(
            'type' => 'Text',
            'name' => 'title',
            'options' => array(
                'label' => /*@translate*/ 'Job title'
            ),
        ));
       
       $this->add(array(
           'type' => 'Textarea',
           'name' => 'description',
           'options' => array(
                'label' => /*@translate*/ 'Job description'
           ),
       ));
       
       $this->add(array(
            'type' => 'Text',
            'name' => 'location',
            'options' => array(
                'label' => /*@translate*/ 'Location'
            ),
        ));
       
       $this->add(array(
            'type' => 'Text',
            'name' => 'contactEmail',
            'options' => array(
                'label' => /*@translate*/ 'Contact email'
            ),
           
        ));
       
       
       $this->add(array(
            'type' => 'Text',
            'name' => 'reference',
            'options' => array(
                'label' => /*@translate*/ 'Reference number'
            ),
            
        ));
       
    }
    
    public function getInputFilterSpecification() 
    {
//         return array(
//             'applyId' => array(
//                 'required' => true,
//                 'filters' => array(
//                     'name' => 'StringTrim'
//                 ),
//                 'validators' => array(
//                     new 
//         )
//             ),
//             'company' => array(
//                 'required' => true,
//             ),
//             ''
//         );
    }

}