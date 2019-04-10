<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Factory\Controller;

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
        $repositories = $this->createMock(RepositoryService::class);

        $container->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['repositories',$repositories],
                ['Core/File/Events',$coreFileEvents]
            ])
        ;

        $factory = new FileControllerFactory();
        $this->assertInstanceOf(
            FileController::class,
            $factory($container, 'some-name')
        );
    }
}
