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

use Auth\Factory\Service\RegisterFactory;
use CoreTest\Bootstrap;

/**
 * Class RegisterSLFactoryTest
 * @package AuthTest\Factory\Service
 * @covers \Auth\Factory\Service\RegisterFactory
 */
class RegisterFactoryTest extends TestCase
{
    /**
     * @var RegisterFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new RegisterFactory();
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
        $this->assertInstanceOf('Auth\Service\Register', $result);
    }
}
