<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace ApplicationsTest\Factory\Auth\Dependency;

use PHPUnit\Framework\TestCase;

use Applications\Factory\Auth\Dependency\ListListenerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Applications\Auth\Dependency\ListListener;
use Applications\Repository\Application as Repository;

/**
 * @coversDefaultClass \Applications\Factory\Auth\Dependency\ListListenerFactory
 */
class ListListenerFactoryTest extends TestCase
{

    /**
     * @covers ::createService
     */
    public function testcreateService()
    {
        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $repositories = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $repositories->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Applications'))
            ->willReturn($repository);
        
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with($this->equalTo('repositories'))
            ->willReturn($repositories);
        
        $listListenerFactory = new ListListenerFactory();
        $this->assertInstanceOf(ListListener::class, $listListenerFactory->createService($serviceLocator));
    }
}
