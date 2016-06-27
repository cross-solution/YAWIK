<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Controller;

use Auth\Factory\Controller\IndexControllerFactory;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class IndexControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IndexControllerFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new IndexControllerFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $authenticationServiceMock = $this->getMockBuilder('Auth\AuthenticationService')
            ->disableOriginalConstructor()
            ->getMock();

        $formMock = $this->getMockBuilder('Auth\Form\Login')
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')
            ->getMock();

        $sm->setService('AuthenticationService', $authenticationServiceMock);
        $sm->setService('Core/Log', $loggerMock);
        $sm->setService('Auth\Form\Login', $formMock);


        $controllerManager = new ControllerManager($sm);

        $result = $this->testedObj->createService($controllerManager);

        $this->assertInstanceOf('Auth\Controller\IndexController', $result);
    }
}
