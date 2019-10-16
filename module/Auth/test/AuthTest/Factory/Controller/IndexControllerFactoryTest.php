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

use Auth\Adapter\ExternalApplication;
use Auth\Adapter\HybridAuth as HybridAuthAdapter;

use Auth\Factory\Controller\IndexControllerFactory;
use Core\Repository\RepositoryService;
use Doctrine\ODM\MongoDB\DocumentManager;
use CoreTest\Bootstrap;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\MvcEvent;

class IndexControllerFactoryTest extends TestCase
{
    /**
     * @var IndexControllerFactory
     */
    private $testedObj;

    protected function setUp(): void
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
        
        $dmMock = $this
            ->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $sm->setService('AuthenticationService', $authenticationServiceMock);
        $sm->setService('Core/Log', $loggerMock);
        $sm->setService('Auth\Form\Login', $formMock);
        
        $hybridAuthAdapter = $this->getMockBuilder(HybridAuthAdapter::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $externalAdapter = $this->getMockBuilder(ExternalApplication::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $repositories = $this->getMockBuilder(RepositoryService::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        
        $sm->setService('HybridAuthAdapter', $hybridAuthAdapter);
        $sm->setService('ExternalApplicationAdapter', $externalAdapter);
        $sm->setService('repositories', $repositories);
        $controllerManager = new ControllerManager($sm);
        $sm->setService('ControllerManager', $controllerManager);
        
        $result = $this->testedObj->createService($sm);

        $this->assertInstanceOf('Auth\Controller\IndexController', $result);
    }
}
