<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */

namespace OrganizationsTest\Factory\ImageFileCache;

use PHPUnit\Framework\TestCase;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Organizations\Factory\ImageFileCache\ODMListenerFactory;
use Organizations\ImageFileCache\ODMListener;
use Organizations\ImageFileCache\Manager;

/**
 * @coversDefaultClass \Organizations\Factory\ImageFileCache\ODMListenerFactory
 */
class ODMListenerFactoryTest extends TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvokation()
    {
        $manager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValueMap([
                ['Organizations\ImageFileCache\Manager', $manager],
            ]));

        $factory = new ODMListenerFactory();
        $listener = $factory->__invoke($serviceLocator, 'irrelevant');
        $this->assertInstanceOf(ODMListener::class, $listener);
    }
}
