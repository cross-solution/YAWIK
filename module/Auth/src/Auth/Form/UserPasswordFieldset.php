<?php

namespace Auth\Form;

//use Core\Entity\Hydrator\EntityHydrator;
use Auth\Form\Hydrator\UserPasswordFieldsetHydrator;
use Auth\Entity\User;
use Zend\Form\Fieldset;
use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class UserPasswordFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new UserPasswordFieldsetHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
	public function init()
    {
        $this->setName('passwordfieldset')
             ->setLabel( /* @translate */ 'Password');
             //->setHydrator(new \Core\Model\Hydrator\ModelHydrator());

        
        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password',
            'options' => array( 'label' => /* @translate */ 'Password' ),
         )); 
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'password2',
            'options' => array( 'label' => /* @translate */ 'Password wiederholen' ),
         ));
               
    }
    
    public function allowObjectBinding($object)
    {
        return $object instanceof User;
    }
    
    /*
    public function setValue($value)
    {
        if ($value instanceOf EntityInterface) {
            if ($value instanceOf RelationEntity) {
                $value = $value->getEntity();
            }
            $data = $this->getHydrator()->extract($value);
            $this->populateValues($data);
            $this->setObject($value);
        }
        return parent::setValue($value);
    }
    */
    
    public function setValidationGroup($name) {
        return parent::setValidationGroup($name);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getInputFilterSpecification()
    {
        return array(
             'password' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => '\Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                            new NotEmpty(),
                            new StringLength(array('max' => 50))
                ),
            ),
             'password2' => array(
                'required' => true,
                'filters'  => array(
                    array('name' => '\Zend\Filter\StringTrim'),
                ),
                'validators' => array(
                            new NotEmpty(),
                            new StringLength(array('max' => 50)),
                            new Identical('password'),
                ),
            ),
        );
        
    }
}