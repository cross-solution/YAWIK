<?php

namespace Auth\Form;

use Zend\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;

class UserBaseFieldset extends Fieldset
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
        $this->setName('base');
             //->setLabel( /* @translate */ 'General');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(
            array(
            'type' => 'Auth/RoleSelect',
            'name' => 'role',
            'options' => array(
                'label' => /* @translate */ 'I am',
            ),
            'attributes' => array(
                'data-trigger' => 'submit'
            ),
            )
        );
        
    }
    
    public function setObject($object)
    {
        parent::setObject($object);
        $this->populateValues($this->extract());
        return $this;
    }
}
