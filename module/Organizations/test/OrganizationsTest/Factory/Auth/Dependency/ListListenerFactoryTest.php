<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace OrganizationsTest\Factory\Auth\Dependency;

use PHPUnit\Framework\TestCase;

use Organizations\Factory\Auth\Dependency\ListListenerFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Organizations\Auth\Dependency\ListListener;
use Organizations\Repository\Organization as Repository;

/**
 * @coversDefaultClass \Organizations\Factory\Auth\Dependency\ListListenerFactory
 */
class ListListenerFactoryTest extends TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvokation()
    {
        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $repositories = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $repositories->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Organizations'))
            ->willReturn($repository);
        
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with($this->equalTo('repositories'))
            ->willReturn($repositories);
        
        $listListenerFactory = new ListListenerFactory();
        $this->assertInstanceOf(ListListener::class, $listListenerFactory->__invoke($serviceLocator, 'irrelevant'));
    }
}
