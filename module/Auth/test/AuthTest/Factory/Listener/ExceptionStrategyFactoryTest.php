<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Listener;

use Auth\Factory\Listener\ExceptionStrategyFactory;
use Auth\Listener\UnauthorizedAccessListener;
use Auth\Listener\DeactivatedUserListener;
use Test\Bootstrap;

class ExceptionStrategyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExceptionStrategyFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new ExceptionStrategyFactory();
    }

    /**
     * @dataProvider canonicalNames
     */
    public function testCreateService($canonicalName, $expectedInstance)
    {
		$sm = clone Bootstrap::getServiceManager();
		$this->assertInstanceOf($expectedInstance, $this->factory->__invoke($sm, $canonicalName));
    }
    
    /**
     * @dataProvider invalidCanonicalNames
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown service
     */
    public function testStatusThrowsExceptionIfInvalidStatusPassed($canonicalName)
    {
        $sm = clone Bootstrap::getServiceManager();
		$this->factory->__invoke($sm, $canonicalName);
    }
    
    public function canonicalNames()
    {
        return [
            [
                'UnauthorizedAccessListener',
                UnauthorizedAccessListener::class
            ],
            [
                'DeactivatedUserListener',
                DeactivatedUserListener::class
            ]
        ];
    }
    
    public function invalidCanonicalNames()
    {
        return [
            [
                'invalid',
            ],
            [
                '',
            ],
            [
                null,
            ]
        ];
    }
}
