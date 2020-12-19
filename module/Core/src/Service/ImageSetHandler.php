<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Entity\ImageSetInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface as ImagineImage;
use Imagine\Image\ImagineInterface;

use Psr\Container\ContainerInterface;

class ImageSetHandler
{
    /**
     * @var ImagineInterface
     */
    private ImagineInterface $imagine;

    public function __construct(
        ImagineInterface $imagine
    )
    {
        $this->imagine = $imagine;
    }

    public static function factory(ContainerInterface $container)
    {
        $imagine = $container->get('Imagine');

        return new self($imagine);
    }

    /**
     * @param $specs
     * @param array $imageData
     * @return array|ImagineImage[]
     */
    public function createImages($specs, array $imageData): array
    {
        $images = [];
        $imagine = $this->imagine;
        $tmpFile = $imageData['tmp_name'];

        $original = $imagine->open($tmpFile);
        $images['original'] = $original;

        foreach($specs as $key => $size){
            $newImage = ImageSetInterface::THUMBNAIL == $key
                ? $original->thumbnail(new Box($size[0], $size[1]), ImagineImage::THUMBNAIL_INSET)
                : $this->createImage($original, $size);

            if ($newImage) {
                $images[$key] = $newImage;
            }
        }

        return $images;
    }

    private function createImage(ImagineImage $image, $size)
    {
        $imageSize = $image->getSize();

        if ($imageSize->getWidth() <= $size[0] && $imageSize->getHeight() <= $size[1]) {
            return null;
        }

        if ($imageSize->getWidth() > $size[0]) {
            $imageSize = $imageSize->widen($size[0]);
        }

        if ($imageSize->getHeight() > $size[1]) {
            $imageSize = $imageSize->heighten($size[1]);
        }

        $image = $image->resize($imageSize);

        return $image;
    }
}