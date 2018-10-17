<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity\Hydrator;

use Core\Entity\ImageSet;
use Core\Entity\ImageSetInterface;
use Core\Options\ImageSetOptions;
use Doctrine\MongoDB\GridFSFile;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * Hydrator for ImageSets.
 *
 * @see \Core\Entity\ImageSetInterface
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ImageSetHydrator implements HydratorInterface
{

    /**
     * Imagine
     *
     * @var \Imagine\Image\ImagineInterface
     */
    protected $imagine;

    /**
     * Options
     *
     * @var ImageSetOptions
     */
    protected $options;

    public function __construct(ImagineInterface $imagine, ImageSetOptions $options)
    {
        $this->imagine = $imagine;
        $this->options = $options;
    }

    /**
     * Extract values from an object
     *
     * @param object $object
     *
     * @return array
     */
    public function extract($object)
    {
        if (!$object instanceof ImageSet || !($image = $object->get(ImageSetInterface::ORIGINAL))) {
            return [];
        }

        return [$this->options->getFormElementName() => $image->getId()];
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        if (!isset($data['original']) || UPLOAD_ERR_OK !== $data['original']['error']) {
            return $object;
        }

        $data = $data['original'];
        $file  = $data['tmp_name'];

        $image = $this->imagine->open($file);
        $imageSpecs = $this->options->getImages();


        $images = [ ImageSetInterface::ORIGINAL => $this->createEntity($file, $data) ];

        foreach ($imageSpecs as $key => $size) {
            $newImage = ImageSetInterface::THUMBNAIL == $key
                ? $image->thumbnail(new Box($size[0], $size[1]), ImageInterface::THUMBNAIL_INSET)
                : $this->createImage($image, $size);

            if ($newImage) {
                $entity   = $this->createEntity($newImage, $data, $key);
                $images[$key] = $entity;
            }
        }

        $object->setImages($images);

        return $object;
    }

    private function createImage(ImageInterface $image, $size)
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

    private function createEntity($image, &$data, $prefix = '')
    {
        /* @var \Core\Entity\ImageInterface $entity */
        $entity = $this->options->getEntity();


        if (is_string($image)) {
            $file = $image;
        } else {
            $format = str_replace('image/', '', $data['type']);

            $file = new GridFSFile();
            $file->setBytes($image->get($format));
        }

        $entity
            ->setFile($file)
            ->setName(($prefix ? "$prefix-" : '') . $data['name'])
            ->setType($data['type'])
        ;

        return $entity;
    }
}
