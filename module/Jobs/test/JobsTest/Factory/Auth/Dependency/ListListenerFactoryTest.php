<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace JobsTest\Factory\Auth\Dependency;

use PHPUnit\Framework\TestCase;

use Jobs\Factory\Auth\Dependency\ListListenerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Jobs\Auth\Dependency\ListListener;
use Jobs\Repository\Job as Repository;

/**
 * @coversDefaultClass \Jobs\Factory\Auth\Dependency\ListListenerFactory
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
            ->with($this->equalTo('Jobs'))
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
