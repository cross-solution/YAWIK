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
use Organizations\Factory\ImageFileCache\ApplicationListenerFactory;
use Organizations\ImageFileCache\ApplicationListener;
use Organizations\ImageFileCache\Manager as CacheManager;
use Core\Service\FileManager as FileManager;

/**
 * @coversDefaultClass \Organizations\Factory\ImageFileCache\ApplicationListenerFactory
 */
class ApplicationListenerFactoryTest extends TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvokation()
    {
        $cacheManager = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $fileManager = $this->createMock(FileManager::class);


        $serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $serviceLocator->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['Organizations\ImageFileCache\Manager', $cacheManager],
                [FileManager::class, $fileManager]
            ]));

        $factory = new ApplicationListenerFactory();
        $listener = $factory->__invoke($serviceLocator, 'irrelevant');
        $this->assertInstanceOf(ApplicationListener::class, $listener);
    }
}
