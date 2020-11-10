<?php

namespace Auth\Form;

use Auth\Form\Validator\UniqueLoginName;
use Laminas\Form\Fieldset;
use Core\Entity\Hydrator\EntityHydrator;
use Laminas\InputFilter\InputFilterProviderInterface;

class UserBaseFieldset extends Fieldset implements InputFilterProviderInterface
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


        $this->add([
            'type' => 'text',
            'name' => 'login',
            'options' => [
                'label' => /* @translate */ 'Login name',
            ],
        ]);
    }

    public function setObject($object)
    {
        parent::setObject($object);
        $this->populateValues($this->extract());
        return $this;
    }

    public function getInputFilterSpecification()
    {
        return [
            'login' => [
                'required' => true,
                'validators' => [
                    ['name' => UniqueLoginName::class]
                ],
                'filters' => [
                    ['name' => 'StringTrim']
                ],
            ],
        ];
    }
}
