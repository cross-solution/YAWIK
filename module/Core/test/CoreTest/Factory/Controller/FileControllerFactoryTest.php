<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace CoreTest\Factory\Controller;

use Core\Service\FileManager;
use PHPUnit\Framework\TestCase;

use Core\Controller\FileController;
use Core\EventManager\EventManager;
use Core\Factory\Controller\FileControllerFactory;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;

/**
 * Class FileControllerFactoryTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Factory\Controller
 * @since 0.30
 */
class FileControllerFactoryTest extends TestCase
{
    public function testInvoke()
    {
        $container = $this->createMock(ContainerInterface::class);
        $coreFileEvents = $this->createMock(EventManager::class);
        $fileManager = $this->createMock(FileManager::class);

        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['Core/File/Events',$coreFileEvents],
                [FileManager::class, $fileManager]
            ])
        ;

        $factory = new FileControllerFactory();
        $this->assertInstanceOf(
            FileController::class,
            $factory($container, 'some-name')
        );
    }
}
