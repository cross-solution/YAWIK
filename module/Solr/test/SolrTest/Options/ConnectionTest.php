<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace SolrTest\Options;

use CoreTestUtils\TestCase\SetterGetterTrait;
use CoreTestUtils\TestCase\SetupTargetTrait;
use Solr\Options\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    use SetterGetterTrait, SetupTargetTrait;

    protected $target = [
        'class' => Connection::class
    ];

    public function propertiesProvider()
    {
        return [
            ['hostname', [
                'value' => 'some-hostname',
                'default' => 'localhost'
            ]],
            ['port', [
                'default' => 80,
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
            ]]
        ];
    }
}
