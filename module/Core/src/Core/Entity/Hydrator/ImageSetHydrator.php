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
use Core\Options\ImageSetOptions;
use Doctrine\MongoDB\GridFSFile;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ImageSetHydrator implements HydratorInterface
{

    /**
     *
     *
     * @var \Imagine\Image\ImagineInterface
     */
    protected $imagine;

    /**
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
     * @param  object $object
     *
     * @return array
     */
    public function extract($object)
    {
        if (!$object instanceOf ImageSet || !($image = $object->getOriginal())) {
            return [];
        }

        return ['original' => $image->getId()];
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
        $thumbnailSize = $this->options->getThumbnailSize();

        $images = [
            'original'  => $this->createEntity($file, $data),
            'thumbnail' => $this->createEntity($this->createImage($image, $this->options->getThumbnailSize()), $data, 'thumbnail-'),
            'large'     => $this->createEntity($this->createImage($image, $this->options->getLargeSize()), $data, 'large-'),
            'mid'       => $this->createEntity($this->createImage($image, $this->options->getMidSize()), $data, 'mid-'),
            'small'     => $this->createEntity($this->createImage($image, $this->options->getSmallSize()), $data, 'small-'),
        ];

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
        if (is_null($image)) { return null; }
        /* @var \Core\Entity\FileEntity $entity */
        $entityClass = $this->options->getEntityClass();
        $entity = new $entityClass();

        if (is_string($image)) {
            $file = $image;
        } else {
            $format = str_replace('image/', '', $data['type']);

            $file = new GridFSFile();
            $file->setBytes($image->get($format));
        }

        $entity
            ->setFile($file)
            ->setName($prefix . $data['name'])
            ->setType($data['type'])
        ;

        return $entity;
    }

}