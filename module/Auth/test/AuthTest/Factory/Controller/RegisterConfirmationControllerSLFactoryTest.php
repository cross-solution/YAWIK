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

use Auth\Factory\Controller\RegisterConfirmationControllerFactory;
use CoreTest\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class RegisterConfirmationControllerSLFactoryTest extends TestCase
{
    /**
     * @var RegisterConfirmationControllerFactory
     */
    private $testedObj;

    protected function setUp(): void
    {
        $this->testedObj = new RegisterConfirmationControllerFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $registerConfirmationServiceMock = $this->getMockBuilder('Auth\Service\RegisterConfirmation')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')
            ->getMock();

        $sm->setService('Auth\Service\RegisterConfirmation', $registerConfirmationServiceMock);
        $sm->setService('Core/Log', $loggerMock);

        $controllerManager = new ControllerManager($sm);

        $result = $this->testedObj->createService($sm);

        $this->assertInstanceOf('Auth\Controller\RegisterConfirmationController', $result);
    }
}
