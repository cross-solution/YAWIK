<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Controller;

use PHPUnit\Framework\TestCase;

use Auth\Factory\Controller\PasswordControllerFactory;
use CoreTest\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class PasswordControllerFactoryTest extends TestCase
{
    /**
     * @var PasswordControllerFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new PasswordControllerFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $authenticationServiceMock = $this->getMockBuilder('Auth\AuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoriesMock = $this->getMockBuilder('Core\Repository\RepositoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $sm->setService('AuthenticationService', $authenticationServiceMock);
        $sm->setService('repositories', $repositoriesMock);

        $controllerManager = new ControllerManager($sm);

        $result = $this->testedObj->createService($sm);

        $this->assertInstanceOf('Auth\Controller\PasswordController', $result);
    }
}
