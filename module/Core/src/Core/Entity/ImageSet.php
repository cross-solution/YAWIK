<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Manages a set of images which belongs together.
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class ImageSet implements ImageSetInterface
{
    use EntityTrait;

    /**
     * The id of this image set.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $id;

    /**
     * Images in this set.
     *
     * @ODM\ReferenceMany(discriminatorField="_entity", cascade="all", orphanRemoval=true)
     * @var Collection
     */
    protected $images;

    /**
     * @internal
     *      Creates a unique {@link $id} for this ImageSet
     */
    public function __construct()
    {
        $this->id = (string) new \MongoId();
    }

    /**
     * Provide convinient methods for get and set.
     *
     * - get<ImageKey> proxies to get(<imageKey>)
     * - set<ImageKey>($image) proxies to set(<imageKey>, $image)
     *
     * @param string $method
     * @param array $args
     *
     * @return $this|ImageInterface|null
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (0 === strpos($method, 'get')) {
            $key = lcfirst(substr($method, 3));
            $fallback = count($args) ? $args[0] : true;

            return $this->get($key, $fallback);
        }

        if (0 === strpos($method, 'set')) {
            if (1 > count($args)) {
                throw new \BadMethodCallException(sprintf(
                    'Missing argument 1 for "%s" in "%s"', $method, get_class($this)
                ));
            }
            $key = lcfirst(substr($method, 3));
            return $this->set($key, $args[0]);
        }

        throw new \BadMethodCallException(sprintf(
            'Unknown method "%s" in "%s"', $method, get_class($this)
        ));
    }

    /**
     * Clear all the images in this set.
     *
     * @return self
     */
    public function clear()
    {
        if ($this->images) {
            $this->images->clear();
        }

        return $this;
    }

    /**
     * @param Collection $images
     *
     * @return self
     */
    public function setImagesCollection(Collection $images)
    {
        $this->clear();
        $this->images = $images;

        return $this;
    }

    /**
     * Set images and permissions.
     *
     * Replaces the whole set!
     *
     * @param array                $images
     * @param PermissionsInterface $permissions
     *
     * @return self
     */
    public function setImages(array $images, PermissionsInterface $permissions = null)
    {
        $this->clear();

        foreach ($images as $prop => $image) {
            $this->set($prop, $image, /* check */ false);
        }

        if ($permissions) {
            $this->setPermissions($permissions);
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    protected function getImages()
    {
        if (!$this->images) {
            $this->images = new ArrayCollection();
        }

        return $this->images;
    }

    /**
     * Get an image
     *
     * If no image with the $key is found, the image with the key
     * self::ORIGINAL is returned. Unless self::ORIGINAL is requested
     * or $fallback is false - in that case NULL is returned.
     *
     * @param string $key
     * @param bool   $fallback true: Return ORIGINAL image, if not found.
     *
     * @return ImageInterface|null
     */
    public function get($key, $fallback = true)
    {
        foreach ($this->getImages() as $image) {
            /* @var ImageInterface $image */
            if ($key == $image->getKey()) {
                return $image;
            }
        }

        return !$fallback || self::ORIGINAL == $key ? null : $this->get(self::ORIGINAL);
    }

    /**
     * Set an image.
     *
     * Replaces any image with the same $key, unless $check is false.
     *
     * @param string         $key
     * @param ImageInterface $image
     * @param bool           $check
     *
     * @return self
     */
    public function set($key, ImageInterface $image, $check = true)
    {
        $images = $this->getImages();
        if ($check && ($img = $this->get($key))) {
            $images->removeElement($img);
        }

        $image->setBelongsTo($this->id);
        $image->setKey($key);

        $images->add($image);

        return $this;
    }

    /**
     * Set permissions for all images in this set.
     *
     * @param PermissionsInterface $permissions
     *
     * @return self
     */
    public function setPermissions(PermissionsInterface $permissions)
    {
        foreach ($this->getImages() as $file) {
            /* @var PermissionsInterface $filePermissions */
            /* @var \Core\Entity\FileInterface $file */
            $filePermissions = $file->getPermissions();
            $filePermissions->clear();
            $filePermissions->inherit($permissions);
        }

        return $this;
    }
}