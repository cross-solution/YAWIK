<?php

namespace Auth\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Fieldset;
use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;
use Core\Form\ViewPartialProviderInterface;
use Zend\InputFilter\InputFilterProviderInterface;

class UserInfoFieldset extends Fieldset implements ViewPartialProviderInterface,
                                                   InputFilterProviderInterface
{
    
    protected $viewPartial = 'form/auth/my-profile';

    public function setViewPartial($partial)
    {
        $this->viewPartial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->viewPartial;
    }
    
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
                'label' => /*@translate*/ 'Email'
            )
        ));
        
        $this->add(array(
        		'name' => 'phone',
        		'options' => array(
        				'label' => /*@translate*/ 'Phone',
        		    'required' => true,
        		),
                'required' => true,
        ));
        
        $this->add(array(
        		'name' => 'postalcode',
        		'options' => array(
        				'label' => /*@translate*/ 'Postalcode'
        		)
        ));
        
        $this->add(array(
        		'name' => 'city',
        		'options' => array(
        				'label' => /*@translate*/ 'City'
        		)
        ));
        
        
        
        $this->add(array(
            'name' => 'firstName',
            'options' => array(
                'label' => /*@translate*/ 'First name',
            ),
        ));
        
        $this->add(array(
            'name' => 'lastName',
            'options' => array(
                'label' => /*@translate*/ 'Last name',
            ),
        ));
        
        $this->add(array(
            'type' => 'file',
            'name' => 'image',
//             'options' => array(
//                 'label' => /*@translate*/ 'Image',
//             ),
        
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
    
    public function getInputFilterSpecification()
    {
        return array(
            
        );
        
    }
    
    
}