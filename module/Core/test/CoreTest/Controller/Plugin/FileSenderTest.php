<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace CoreTest\Controller\Plugin;

use Core\Entity\FileInterface;
use Core\Entity\FileMetadataInterface;
use Core\Service\FileManager;
use PHPUnit\Framework\TestCase;

use Core\Controller\AbstractCoreController;
use Core\Controller\Plugin\FileSender;
use Core\Entity\FileEntity;
use Core\Repository\RepositoryService;
use Doctrine\Persistence\ObjectRepository;
use Interop\Container\ContainerInterface;
use Laminas\Http\PhpEnvironment\Response;

/**
 * Class FileSenderTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Controller\Plugin
 * @covers \Core\Controller\Plugin\FileSender
 * @since 0.30.1
 */
class FileSenderTest extends TestCase
{
    public function testFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $fileManager = $this->createMock(FileManager::class);
        $container->expects($this->once())
            ->method('get')
            ->with(FileManager::class)
            ->willReturn($fileManager)
        ;
        $ob = FileSender::factory($container);
        $this->assertInstanceOf(FileSender::class, $ob);
    }

    public function testSendFile()
    {
        $fileManager = $this->createMock(FileManager::class);
        $controller = $this->createMock(AbstractCoreController::class);
        $file = $this->createMock(FileInterface::class);
        $metadata = $this->createMock(FileMetadataInterface::class);

        $response = new Response();

        $fileId = 'someId';

        $fileManager->expects($this->any())
            ->method('findByID')
            ->with('class', $fileId)
            ->willReturn(null, $file)
        ;
        $controller->expects($this->any())
            ->method('getResponse')
            ->willReturn($response)
        ;

        $sender = new FileSender($fileManager);
        $sender->setController($controller);

        // fist test: response will be 404 if file is not found
        $sender('class', $fileId);
        $this->assertEquals(404, $response->getStatusCode());

        // second test: will handle send file properly
        $file->expects($this->any())
            ->method('getMetadata')
            ->willReturn($metadata);
        $metadata->expects($this->any())
            ->method('getContentType')
            ->willReturn('type')
        ;
        $file->expects($this->any())
            ->method('getLength')
            ->willReturn(1024);

        $resource = fopen(__FILE__, 'r');
        $fileManager->expects($this->once())
            ->method('getStream')
            ->with($file)
            ->willReturn($resource)
        ;

        ob_start();
        $sender('class', $fileId);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertStringEqualsFile(__FILE__, $output);
    }
}
