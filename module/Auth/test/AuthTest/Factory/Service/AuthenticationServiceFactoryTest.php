<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Service;

use PHPUnit\Framework\TestCase;

use Auth\Factory\Service\AuthenticationServiceFactory;
use CoreTest\Bootstrap;

/**
 * Class AuthenticationServiceFactoryTest
 * @package AuthTest\Factory\Service
 * @covers \Auth\Factory\Service\AuthenticationServiceFactory
 */
class AuthenticationServiceFactoryTest extends TestCase
{
    /**
     * @var AuthenticationServiceFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new AuthenticationServiceFactory();
    }

    public function testCreateService()
    {
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

        $sm = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $sm->expects($this->once())
            ->method('get')->with('repositories')->willReturn($repositoriesMock);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\AuthenticationService', $result);
    }
}
