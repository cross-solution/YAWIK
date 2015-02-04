<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Service\SLFactory;

use Auth\Service\SLFactory\RegisterConfirmationSLFactory;
use Test\Bootstrap;

class RegisterConfirmationSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterConfirmationSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new RegisterConfirmationSLFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $userRepositoryMock = $this->getMockBuilder('Auth\Repository\User')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock->expects($this->once())
            ->method('get')
            ->with('Auth/User')
            ->willReturn($userRepositoryMock);

        $sm->setService('repositories', $repositoriesMock);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Service\RegisterConfirmation', $result);
    }
}