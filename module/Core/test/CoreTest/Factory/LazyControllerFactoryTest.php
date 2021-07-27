<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace CoreTest\Factory;

use Core\Service\FileManager;
use PHPUnit\Framework\TestCase;

use Core\Controller\ContentController;
use Core\Controller\FileController;
use Core\Controller\IndexController;
use Core\EventManager\EventManager;
use Core\Factory\Controller\LazyControllerFactory;
use Core\Repository\RepositoryService;
use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use Interop\Container\ContainerInterface;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

/**
 * Class LazyControllerFactoryTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30
 * @covers \Core\Factory\Controller\LazyControllerFactory
 * @package CoreTest\Factory
 */
class LazyControllerFactoryTest extends TestCase
{
    use ServiceManagerMockTrait;

    /**
     * @dataProvider invokeTestProvider
     */
    public function testInvoke($requestedName, $expectedClass)
    {
        $moduleManager = $this->createMock(ModuleManagerInterface::class);
        $config = [];
        $repositories = $this->createMock(RepositoryService::class);
        $eventManager = $this->createMock(EventManager::class);
        $fileManager = $this->createMock(FileManager::class);

        $eventManager->expects($this->any())
            ->method('getEvent')
            ->willReturnSelf()
        ;
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->any())
            ->method('has')
            ->willReturn(true)
        ;
        $container->expects($this->any())
            ->method('get')
            ->willReturnMap([
                ['config',$config],
                ['ModuleManager',$moduleManager],
                ['repositories',$repositories],
                ['Core/EventManager',$eventManager],
                [FileManager::class, $fileManager]
            ])
        ;

        $factory = new LazyControllerFactory();
        $this->assertInstanceOf(
            $expectedClass,
            $factory($container, $requestedName)
        );
    }

    public function invokeTestProvider()
    {
        return [
            ['Core/Index',IndexController::class],
            ['Core/File',FileController::class],
            ['Core/Content',ContentController::class],
        ];
    }

    public function testThrowsWhenControllerClassNotFound()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessageRegExp('/Can\'t find correct controller class for/');
        $factory = new LazyControllerFactory();
        $factory($container, 'Core/Foo');
    }

    public function testThrowsWhenCanNotCreateConstructorArgument()
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessageRegExp('/Can\'t create constructor argument/m');
        $factory = new LazyControllerFactory();
        $factory($container, 'Core/Index');
    }

    public function testCanCreate()
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new LazyControllerFactory();
        $this->assertFalse($factory->canCreate($container, 'Foo\\Bar'));
        $this->assertTrue($factory->canCreate($container, 'Foo\\Controller\\Bar'));
    }
}
