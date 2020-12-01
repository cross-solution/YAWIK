<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Options;

use Core\Entity\FileMetadataInterface;
use Core\Entity\Image;
use Core\Entity\ImageMetadata;
use Core\Entity\ImageSetInterface;
use Laminas\Stdlib\AbstractOptions;

/**
 * Options for configuring ImageSetHydrator instances.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,29
 */
class ImageSetOptions extends AbstractOptions
{

    /**
     * Entity class for images.
     */
    protected string $entityClass = Image::class;

    protected string $metadataClass = ImageMetadata::class;

    /**
     * Name of the form element.
     */
    protected string $formElementName = ImageSetInterface::ORIGINAL;

    /**
     * Image specifications.
     */
    protected array $images = [
        ImageSetInterface::THUMBNAIL => [100,100],
    ];

    /**
     * @param string $entityClass
     *
     * @return self
     */
    public function setEntityClass(string $entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return string
     */
    public function getMetadataClass(): string
    {
        return $this->metadataClass;
    }

    public function getMetadata(): FileMetadataInterface
    {
        return new $this->metadataClass();
    }

    /**
     * @param string $formElementName
     *
     * @return self
     */
    public function setFormElementName($formElementName)
    {
        $this->formElementName = $formElementName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormElementName()
    {
        return $this->formElementName;
    }

    /**
     * Set image specifications.
     *
     * <pre>
     * [
     *      <imageKey> => [<maxWidth>,<maxHeight>],
     * ]
     * </pre>
     *
     * @param array $images
     *
     * @return self
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }
}
