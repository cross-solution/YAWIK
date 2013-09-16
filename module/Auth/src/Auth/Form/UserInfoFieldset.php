<?php

namespace Auth\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Fieldset;
use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;
//use Zend\InputFilter\InputFilterProviderInterface;

class UserInfoFieldset extends Fieldset
{
    
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
        $this->setName('user-info')
             ->setLabel('Informations');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(array(
            'name' => 'email',
            'options' => array(
                'label' => /*translate*/ 'Email'
            )
        ));
        
        $this->add(array(
        		'name' => 'phone',
        		'options' => array(
        				'label' => /*translate*/ 'Phone'
        		)
        ));
        
        $this->add(array(
        		'name' => 'postalcode',
        		'options' => array(
        				'label' => /*translate*/ 'Postalcode'
        		)
        ));
        
        $this->add(array(
        		'name' => 'city',
        		'options' => array(
        				'label' => /*translate*/ 'City'
        		)
        ));
        
        
        
        $this->add(array(
            'name' => 'firstName',
            'options' => array(
                'label' => /*translate*/ 'First name',
            ),
        ));
        
        $this->add(array(
            'name' => 'lastName',
            'options' => array(
                'label' => /*translate*/ 'Last name',
            ),
        ));
     
        
    }
    
    public function setValue($value)
    {
        if ($value instanceOf EntityInterface) {
            if ($value instanceOf RelationEntity) {
                $value = $value->getEntity();
            }
            $data = $this->getHydrator()->extract($value);
            $this->populateValues($data);
        }
        return parent::setValue($value);
    }
    
    
}