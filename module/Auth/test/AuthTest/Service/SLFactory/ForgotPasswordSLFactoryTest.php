<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Service\SLFactory;

use Auth\Service\SLFactory\ForgotPasswordSLFactory;
use Test\Bootstrap;

class ForgotPasswordSLFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ForgotPasswordSLFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new ForgotPasswordSLFactory();
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
        $this->assertInstanceOf('Auth\Service\ForgotPassword', $result);
    }
}