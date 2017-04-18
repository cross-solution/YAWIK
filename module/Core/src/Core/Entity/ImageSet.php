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
 * ${CARET}
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ImageSet implements ImageSetInterface
{
    use EntityTrait;

    /**
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $id;

    /**
     *
     * @ODM\ReferenceMany(discriminatorField="_entity", cascade="all", orphanRemoval=true)
     * @var Collection
     */
    protected $images;

    public function __construct()
    {
        $this->id = new \MongoId();
    }

    public function __call($method, $args)
    {
        if (0 === strpos($method, 'get')) {
            $key = lcfirst(substr($method, 3));
            return $this->get($key);
        }

        throw new \BadMethodCallException('Unknowm method "' . $method . '" in "' . get_class($this));
    }

    public function clear()
    {
        if ($this->images) {
            $this->images->clear();
        }

        return $this;
    }

    public function setImagesCollection(Collection $images)
    {
        $this->clear();
        $this->images = $images;

        return $this;
    }

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

    protected function getImages()
    {
        if (!$this->images) {
            $this->images = new ArrayCollection();
        }

        return $this->images;
    }

    public function get($key)
    {
        foreach ($this->getImages() as $image) {
            /* @var ImageInterface $image */
            if ($key == $image->getKey()) {
                return $image;
            }
        }

        return null;
    }

    public function set($key, ImageInterface $image, $check = true)
    {
        if ($check && ($img = $this->get($key))) {
            $this->images->remove($img);
        }

        $image->setBelongsTo($this->id);
        $image->setKey($key);

        $this->images->add($image);

        return $this;
    }


    public function setPermissions(PermissionsInterface $permissions)
    {
        foreach ($this->getImages() as $file) {
            /* @var PermissionsInterface $filePermissions */
            /* @var \Core\Entity\FileInterface $file */
            $filePermissions = $file->getPermissions();
            $filePermissions->clear();
            $filePermissions->inherit($permissions);
        }
    }
}