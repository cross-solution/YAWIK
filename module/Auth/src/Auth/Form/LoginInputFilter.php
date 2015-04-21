<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Form;

use Auth\Entity\User;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class LoginInputFilter extends InputFilter
{
    public function __construct()
    {
        $factory = new Factory();

        $this->add(
            $factory->createInputFilter(
                array(
                    'name' => array(
                        'name' => 'name',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 3,
                                    'max' => 255,
                                ),
                            ),
                        ),
                    ),
                    'password' => array(
                        'name' => 'email',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 3,
                                    'max' => 255,
                                ),
                            ),
                        ),
                    ),
                )
            ),
            'register'
        );
    }
}