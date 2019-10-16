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

use Auth\Factory\Service\ForgotPasswordFactory;
use Core\EventManager\EventManager;
use CoreTest\Bootstrap;

class ForgotPasswordFactoryTest extends TestCase
{
    /**
     * @var ForgotPasswordFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new ForgotPasswordFactory();
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

        $events = new EventManager();
        $sm->setService('Auth/Events', $events);

        $result = $this->testedObj->createService($sm);
        $this->assertInstanceOf('Auth\Service\ForgotPassword', $result);
        $this->assertSame($events, $result->getEventManager());
    }
}
