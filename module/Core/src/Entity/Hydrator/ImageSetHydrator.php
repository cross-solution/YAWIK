<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Entity\Hydrator;

use Core\Entity\ImageSet;
use Core\Entity\ImageSetInterface;
use Core\Service\FileManager;
use Core\Options\ImageSetOptions;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface as ImagineImage;
use Core\Entity\ImageInterface;
use Imagine\Image\ImagineInterface;
use Laminas\Hydrator\HydratorInterface;

/**
 * Hydrator for ImageSets.
 *
 * @see \Core\Entity\ImageSetInterface
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ImageSetHydrator implements HydratorInterface
{
    protected ImagineInterface $imagine;
    protected ImageSetOptions $options;
    protected FileManager $fileManager;

    public function __construct(FileManager $fileManager, ImagineInterface $imagine, ImageSetOptions $options)
    {
        $this->imagine = $imagine;
        $this->options = $options;
        $this->fileManager = $fileManager;
    }

    /**
     * Extract values from an object
     *
     * @param object $object
     *
     * @return array
     */
    public function extract($object): array
    {
        if (!$object instanceof ImageSetInterface || !($image = $object->get(ImageSetInterface::ORIGINAL))) {
            return [];
        }

        return [$this->options->getFormElementName() => $image->getId()];
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array  $data
     * @param  object|ImageSetInterface $object
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

        if(!is_readable($file)){
            throw new \Exception("File '$file' is unreadable.");
        }

        $image = $this->imagine->open($file);
        $imageSpecs = $this->options->getImages();
        $images = [];

        // add original image
        $images['original'] = $this->createGridFSFile($object->getId(), $file, $data);

        foreach ($imageSpecs as $key => $size) {
            $newImage = ImageSetInterface::THUMBNAIL == $key
                ? $image->thumbnail(new Box($size[0], $size[1]), ImagineImage::THUMBNAIL_INSET)
                : $this->createImage($image, $size);

            if ($newImage) {
                $entity = $this->createGridFSFile($object->getId(), $newImage, $data, $key);
                $images[$key] = $entity;
            }
        }

        /* @var ImageInterface $image */
        foreach($images as $key => $image) {
            $object->add($image);
        }

        return $object;
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

    /**
     * @param string $imageSetId
     * @param string|ImagineImage $image
     * @param array $data
     * @param string $prefix
     *
     * @return void
     */
    private function createGridFSFile(string $imageSetId, $image, array &$data, string $prefix = "")
    {
        /* @var \Core\Entity\ImageInterface $entity */
        /* @var \Core\Entity\ImageMetadata $metadata */
        $metadata = $this->options->getMetadata();
        $fileManager = $this->fileManager;
        $name = ($prefix ? "$prefix-" : '') . $data['name'];
        $key = "" == $prefix ? "original":$prefix;
        $metadata->setContentType($data['type']);
        $metadata->setBelongsTo($imageSetId);
        $metadata->setKey($key);
        $entityClass = $this->options->getEntityClass();

        if (is_string($image)) {
            $file = $image;
        }else{
            $format = str_replace('image/', '', $data['type']);
            $content = $image->get($format);
            $file = '/tmp/php'.md5($content);
            file_put_contents($file, $content);
        }

        return $fileManager->uploadFromFile($entityClass, $metadata, $file, $name);
    }
}
