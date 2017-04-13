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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ImageSet implements EntityInterface
{
    use EntityTrait;

    /**
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $id;

    public function __construct()
    {
        $this->id = new \MongoId();
    }

    /**
     *
     * @ODM\ReferenceOne(discriminatorField="_entity", cascade="all", orphanRemoval=true)
     * @var \Core\Entity\ImageInterface
     */
    protected $original;

    /**
     *
     * @ODM\ReferenceOne(discriminatorField="_entity", cascade="all", orphanRemoval=true)
     * @var \Core\Entity\ImageInterface
     */
    protected $large;

    /**
     *
     * @ODM\ReferenceOne(discriminatorField="_entity", cascade="all", orphanRemoval=true)
     * @var \Core\Entity\ImageInterface
     */
    protected $mid;

    /**
     *
     * @ODM\ReferenceOne(discriminatorField="_entity", cascade="all", orphanRemoval=true)
     * @var \Core\Entity\ImageInterface
     */
    protected $small;

    /**
     *
     * @ODM\ReferenceOne(discriminatorField="_entity", cascade="all", orphanRemoval=true)
     * @var \Core\Entity\ImageInterface
     */
    protected $thumbnail;


    public function setImages(array $images, PermissionsInterface $permissions = null)
    {
        foreach ($images as $prop => $image) {
            if ($image) {
                $image->setBelongsTo($this->id);
                $this->$prop = $image;
            }
        }

        if ($permissions) {
            $this->setPermissions($permissions);
        }

        return $this;
    }

    /**
     * @return \Core\Entity\ImageInterface
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return \Core\Entity\ImageInterface
     */
    public function getLarge()
    {
        return $this->large ?: $this->original;
    }

    /**
     * @return \Core\Entity\ImageInterface
     */
    public function getMid()
    {
        return $this->mid ?: $this->original;
    }

    /**
     * @return \Core\Entity\ImageInterface
     */
    public function getSmall()
    {
        return $this->small ?: $this->original;
    }

    /**
     * @return \Core\Entity\ImageInterface
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    public function remove($repository)
    {
        $this->loop(function($file) use ($repository) {
            /* @var \Core\Repository\RepositoryInterface|\Core\Repository\RepositoryService $repository */
            $repository->remove($file);
        });

        return $this;
    }

    public function setPermissions(PermissionsInterface $permissions)
    {
        $this->loop(function($file) use ($permissions) {
            /* @var PermissionsInterface $filePermissions */
            /* @var \Core\Entity\FileInterface $file */
            $filePermissions = $file->getPermissions();
            $filePermissions->clear();
            $filePermissions->inherit($permissions);
        });
    }

    private function loop(\Closure $function)
    {
        foreach (['original', 'large', 'mid', 'small', 'thumbnail'] as $prop) {
            $file = $this->$prop;
            if ($file) {
                $function($file);
            }
        }
    }
}