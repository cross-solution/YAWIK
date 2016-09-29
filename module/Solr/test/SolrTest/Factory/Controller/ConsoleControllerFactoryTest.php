<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace SolrTest\Factory\Controller;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\ControllerManager;
use Solr\Factory\Controller\ConsoleControllerFactory;
use Solr\Bridge\Manager;
use Solr\Options\ModuleOptions;
use SolrClient;
use Jobs\Repository\Job as JobRepository;
use Solr\Controller\ConsoleController;
use Core\Console\ProgressBar;

/**
 * @coversDefaultClass \Solr\Factory\Controller\ConsoleControllerFactory
 */
class ConsoleControllerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $client = $this->getMockBuilder(SolrClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $manager = $this->getMockBuilder(Manager::class)
            ->setConstructorArgs([new ModuleOptions()])
            ->setMethods(['getClient'])
            ->getMock();
        $manager->expects($this->once())
            ->method('getClient')
            ->willReturn($client);
        
        $repository = $this->getMockBuilder(JobRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $repositories = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $repositories->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Jobs/Job'))
            ->willReturn($repository);
            
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['Solr/Manager', $manager],
                ['repositories', $repositories]
            ]));
        
        $controllerManager = $this->getMockBuilder(ControllerManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $controllerManager->expects($this->once())
            ->method('getServiceLocator')
            ->willReturn($serviceLocator);
        
        $controllerFactory = new ConsoleControllerFactory();
        $controller = $controllerFactory->createService($controllerManager);
        $this->assertInstanceOf(ConsoleController::class, $controller);
        $this->assertInstanceOf(ProgressBar::class, $controller->getProgressBarFactory()->__invoke(0, 'preventOutput'));
    }
}
