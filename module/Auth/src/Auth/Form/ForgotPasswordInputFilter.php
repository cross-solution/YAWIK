<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;

class ForgotPasswordInputFilter extends InputFilter
{
    public function __construct()
    {
        $factory = new InputFactory();
        $this->add(
            $factory->createInput(
                [
                    'name' => 'identity',
                    'required' => true,
                    'filters' => [
                        ['name' => 'StripTags'],
                        ['name' => 'StringTrim'],
                    ],
                    'validators' => [
                        [
                            'name' => 'StringLength',
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min' => 3,
                                'max' => 255,
                            ],
                        ],
                    ],
                ]
            )
        );

    }
}