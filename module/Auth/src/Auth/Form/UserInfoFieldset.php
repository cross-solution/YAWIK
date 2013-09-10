<?php

namespace Auth\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Zend\Form\Fieldset;
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
    
    
}