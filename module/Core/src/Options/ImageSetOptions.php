<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Options;

use Core\Entity\Image;
use Core\Entity\ImageSetInterface;
use Zend\Stdlib\AbstractOptions;

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
     *
     * @var string
     */
    protected $entityClass = Image::class;

    /**
     * Name of the form element.
     *
     * @var string
     */
    protected $formElementName = ImageSetInterface::ORIGINAL;

    /**
     * Image specifications.
     *
     * @var array
     */
    protected $images = [
        ImageSetInterface::THUMBNAIL => [100,100],
    ];

    /**
     * @param string $entityClass
     *
     * @return self
     */
    public function setEntityClass($entityClass)
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
     * @return \Core\Entity\ImageInterface
     */
    public function getEntity()
    {
        $class = $this->getEntityClass();

        return new $class();
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
