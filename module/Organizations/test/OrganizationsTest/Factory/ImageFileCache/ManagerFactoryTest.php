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
use Organizations\Factory\ImageFileCache\ManagerFactory;
use Organizations\ImageFileCache\Manager;
use Organizations\Options\ImageFileCacheOptions;

/**
 * @coversDefaultClass \Organizations\Factory\ImageFileCache\ManagerFactory
 */
class ManagerFactoryTest extends TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvokation()
    {
        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValueMap([
                ['Organizations/ImageFileCacheOptions', new ImageFileCacheOptions()],
            ]));

        $factory = new ManagerFactory();
        $listener = $factory->__invoke($serviceLocator, 'irrelevant');
        $this->assertInstanceOf(Manager::class, $listener);
    }
}
