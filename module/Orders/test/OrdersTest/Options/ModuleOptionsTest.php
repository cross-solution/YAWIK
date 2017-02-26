<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Orders\Options;

use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Orders\Options\ModuleOptions;

/**
 * Class ModuleOptionsTest
 *
 * @author  Carsten Bleek <bleek@cross-solution.de>
 * @since   0.29
 * @covers  Orders\Options\ModuleOptions
 * @package Orders\Options
 */
class ModuleOptionsTest extends \PHPUnit_Framework_TestCase
{
    use TestSetterGetterTrait, SetupTargetTrait;

    protected $target = [
        'class' => ModuleOptions::class
    ];

    public function propertiesProvider()
    {
        return [
            ['currency', [
                'value' => 'USD',
                'default' => 'EUR'
            ]],
            ['currencySymbol', [
                'value' => '$',
                'default' => '€'
            ]],

            ['taxRate', [
                'value' => '21',
                'default' => '19'
            ]],


        ];
    }
}
