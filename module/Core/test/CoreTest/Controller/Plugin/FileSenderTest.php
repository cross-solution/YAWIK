<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace CoreTest\Controller\Plugin;

use Core\Controller\AbstractCoreController;
use Core\Controller\Plugin\FileSender;
use Core\Entity\FileEntity;
use Core\Repository\RepositoryService;
use Doctrine\Common\Persistence\ObjectRepository;
use Interop\Container\ContainerInterface;
use Zend\Http\PhpEnvironment\Response;

/**
 * Class FileSenderTest
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package CoreTest\Controller\Plugin
 * @covers \Core\Controller\Plugin\FileSender
 * @since 0.30.1
 */
class FileSenderTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $repositories = $this->createMock(RepositoryService::class);
        $container->expects($this->once())
            ->method('get')
            ->with('repositories')
            ->willReturn($repositories)
        ;
        $ob = FileSender::factory($container);
        $this->assertInstanceOf(FileSender::class,$ob);
    }

    public function testSendFile()
    {
        $repositories = $this->createMock(RepositoryService::class);
        $repo = $this->createMock(ObjectRepository::class);
        $controller = $this->createMock(AbstractCoreController::class);
        $file = $this->createMock(FileEntity::class);
        $response = new Response();

        $fileId = 'someId';

        $repositories->expects($this->any())
            ->method('get')
            ->with('someRepository')
            ->willReturn($repo)
        ;
        $controller->expects($this->any())
            ->method('getResponse')
            ->willReturn($response)
        ;
        $repo->expects($this->any())
            ->method('find')
            ->willReturn(null,$file)
        ;

        $sender = new FileSender($repositories);
        $sender->setController($controller);

        // fist test: response will be 404 if file is not found
        $sender('someRepository',$fileId);
        $this->assertEquals(404,$response->getStatusCode());

        // second test: will handle send file properly
        $file->expects($this->any())
            ->method('__get')
            ->willReturnMap([
                ['type','type'],
                ['size','size']
            ])
        ;
        $resource = fopen(__FILE__,'r');
        $file->expects($this->once())
            ->method('getResource')
            ->willReturn($resource)
        ;
        ob_start();
        $sender('someRepository',$fileId);
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertStringEqualsFile(__FILE__,$output);
    }
}
