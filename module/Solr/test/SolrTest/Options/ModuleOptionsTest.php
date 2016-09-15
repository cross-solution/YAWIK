<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Options;

use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Solr\Options\ModuleOptions;

/**
 * Class ModuleOptionsTest
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @author  Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author  Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since   0.26
 * @covers  Solr\Options\ModuleOptions
 * @package SolrTest\Options
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
            ['hostname', [
                'value' => 'some-hostname',
                'default' => 'localhost'
            ]],
            ['port', [
                'default' => 8983,
                'value' => 4568
            ]],
            ['path', [
                'default' => '/solr',
                'value' => '/some-path'
            ]],
            ['username', [
                'default' => '',
                'value' => 'some_username',
            ]],
            ['password', [
                'default' => '',
                'value' => 'some_password'
            ]],
            ['secure',[
                'default' => false,
                'value' => true,
                'getter_method' => 'is*',
            ]],
            ['jobsPath', [
                'default' => '/solr/YawikJobs',
                'value' => '/some/Path',
            ]]
        ];
    }
}
