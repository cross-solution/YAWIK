<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\Factory\ImageFileCache;

use Zend\ServiceManager\ServiceLocatorInterface;
use Organizations\Factory\ImageFileCache\ApplicationListenerFactory;
use Organizations\ImageFileCache\ApplicationListener;
use Organizations\ImageFileCache\Manager;
use Organizations\Repository\OrganizationImage as ImageRepository;

/**
 * @coversDefaultClass \Organizations\Factory\ImageFileCache\ApplicationListenerFactory
 */
class ApplicationListenerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvokation()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $repository = $this->getMockBuilder(ImageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $repositories = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $repositories->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Organizations/OrganizationImage'))
            ->willReturn($repository);
            
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['Organizations\ImageFileCache\Manager', $manager],
                ['repositories', $repositories]
            ]));
        
        $factory = new ApplicationListenerFactory();
        $listener = $factory->__invoke($serviceLocator,'irrelevant');
        $this->assertInstanceOf(ApplicationListener::class, $listener);
    }
}
