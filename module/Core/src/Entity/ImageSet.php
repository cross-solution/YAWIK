<?php

declare(strict_types=1);

namespace Core\Entity;

use Core\Entity\Collection\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

/**
 * Class ImageSet
 *
 * @ODM\EmbeddedDocument
 *
 * @package Core\Entity
 */
class ImageSet implements ImageSetInterface
{
    use EntityTrait, IdentifiableEntityTrait;

    /**
     * Images in this set.
     *
     * @ODM\ReferenceMany(discriminatorField="_entity", cascade={"all"}, orphanRemoval=true)
     */
    protected Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->id = (string) new ObjectId();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function setImages($images): self
    {
        $this->images = $images;
        return $this;
    }

    public function clear(): self
    {
        $this->images->clear();

        return $this;
    }

    public function add(ImageInterface $image): self
    {
        $images = $this->images;
        $metadata = $image->getMetadata();
        if(!is_null($current = $this->get($metadata->getKey(), false))){
            $index = $images->indexOf($current);
            $images->remove($index);
        }
        $images->add($image);
        return $this;
    }

    public function get(string $key, bool $fallback = true): ?ImageInterface
    {
        foreach($this->images as $image){
            try{
                if(!is_null($metadata = $image->getMetadata())){
                    if($metadata->getKey() === $key){
                        return $image;
                    }
                }
            }catch (\Exception $e){
                return null;
            }
        }
        return !$fallback || self::ORIGINAL == $key ? null : $this->get(self::ORIGINAL);
    }

    public function getOriginal(): ?ImageInterface
    {
        return $this->get(self::ORIGINAL, false);
    }

    public function getThumbnail()
    {
        return $this->get(self::THUMBNAIL);
    }
}