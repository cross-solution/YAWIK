<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Form;

use Auth\Form\Hydrator\UserPasswordFieldsetHydrator;
use Auth\Entity\User;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\StringLength;

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
        $this->setName('passwordFieldset')
            ->setLabel(/* @translate */ 'Password');

        $this->add(
            array(
            'type' => 'Laminas\Form\Element\Password',
            'name' => 'password',
            'options' => array(
                'label' => /* @translate */ 'Password'
            ),
            )
        );

        $this->add(
            array(
            'type' => 'Laminas\Form\Element\Password',
            'name' => 'password2',
            'options' => array(
                'label' => /* @translate */ 'Retype password'
            ),
            )
        );
    }

    public function allowObjectBinding($object)
    {
        return $object instanceof User;
    }

    /**
     * (non-PHPdoc)
     * @see \Laminas\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getInputFilterSpecification()
    {
        return array(
            'password' => array(
                'required' => true,
                'filters' => array(
                    array('name' => '\Laminas\Filter\StringTrim'),
                    array('name' => '\Laminas\Filter\StripTags'),
                ),
                'validators' => array(
                    new NotEmpty(),
                    new StringLength(array('min' => 3, 'max' => 50))
                ),
            ),
            'password2' => array(
                'required' => true,
                'filters' => array(
                    array('name' => '\Laminas\Filter\StringTrim'),
                ),
                'validators' => array(
                    new NotEmpty(),
                    new StringLength(array('min' => 3, 'max' => 50)),
                    new Identical('password'),
                ),
            ),
        );
    }
}
