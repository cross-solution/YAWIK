<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace AuthTest\Factory\Controller;

use Auth\Factory\Controller\ForgotPasswordControllerFactory;
use Test\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;

class ForgotPasswordControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ForgotPasswordControllerFactory
     */
    private $testedObj;

    public function setUp()
    {
        $this->testedObj = new ForgotPasswordControllerFactory();
    }

    public function testCreateService()
    {
        $sm = clone Bootstrap::getServiceManager();
        $sm->setAllowOverride(true);

        $forgotPasswordMock = $this->getMockBuilder('Auth\Service\ForgotPassword')
            ->disableOriginalConstructor()
            ->getMock();

        $loggerMock = $this->getMockBuilder('Zend\Log\LoggerInterface')
            ->getMock();

        $sm->setService('Auth\Service\ForgotPassword', $forgotPasswordMock);
        $sm->setService('Core/Log', $loggerMock);
        $controllerManager = new ControllerManager($sm);
	    $sm->setService('ControllerManager',$controllerManager);

        $result = $this->testedObj->createService($sm);

        $this->assertInstanceOf('Auth\Controller\ForgotPasswordController', $result);
    }
}
