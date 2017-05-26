<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Geo\Options;

use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Geo\Options\ModuleOptions;

/**
 * Class ModuleOptionsTest
 *
 * @author  Carsten Bleek <bleek@cross-solution.de>
 * @since   0.29
 * @covers  Geo\Options\ModuleOptions
 * @package Geo\Options
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
            ['plugin', [
                'value' => 'geo',
                'default' => 'photon'
            ]],
            ['geoCoderUrl', [
                'value' => 'http://api.cross-solution.de/geo',
                'default' => 'http://photon.yawik.org/api'
            ]],
            ['country', [
                'value' => 'CH',
                'default' => 'DE'
            ]],
        ];
    }
}
