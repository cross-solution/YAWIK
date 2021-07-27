<?php

declare(strict_types=1);

namespace CoreTest\Service;

use Core\Entity\ImageSetInterface;
use Core\Service\ImageSetHandler;
use Imagine\Image\BoxInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ImageSetHandlerTest extends TestCase
{
    public function testFactory()
    {
        $container = $this->createMock(ContainerInterface::class);
        $imagine = $this->createMock(ImagineInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with('Imagine')
            ->willReturn($imagine);

        $this->assertInstanceOf(ImageSetHandler::class, ImageSetHandler::factory($container));
    }

    public function testResizeImage()
    {
        $imagine = $this->createMock(ImagineInterface::class);
        $image = $this->createMock(ImageInterface::class);
        $handler = new ImageSetHandler($imagine);
        $file = __FILE__;

        $specs = [
            ImageSetInterface::ORIGINAL => [100,100],
            ImageSetInterface::THUMBNAIL => [100,100],
        ];
        $imageData = [
            'tmp_name' => $file,
        ];
        $imagine->expects($this->once())
            ->method('open')
            ->with($file)
            ->willReturn($image);

        $image->expects($this->once())
            ->method('thumbnail')
            ->willReturn($image);

        // thumbnail verification
        $box = $this->createMock(BoxInterface::class);
        $image->expects($this->once())
            ->method('getSize')
            ->willReturn($box);
        $box->expects($this->any())
            ->method('getWidth')
            ->willReturn(200);
        $box->expects($this->any())
            ->method('getHeight')
            ->willReturn(200);

        $box->expects($this->once())
            ->method('widen')
            ->with(100)
            ->willReturn($box);
        $box->expects($this->once())
            ->method('heighten')
            ->with(100)
            ->willReturn($box);
        $image->expects($this->once())
            ->method('resize')
            ->with($box)
            ->willReturn($image);

        $handler->createImages($specs, $imageData);
    }
}
