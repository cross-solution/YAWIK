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

use Core\Entity\FileEntity;
use Zend\Stdlib\AbstractOptions;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ImageSetOptions extends AbstractOptions
{

    protected $entityClass = FileEntity::class;

    protected $thumbnailSize = [100, 100];

    protected $largeSize = [1200, 1200];

    protected $midSize = [600, 600];

    protected $smallSize = [300, 300];

    /**
     * @param mixed $entityClass
     *
     * @return self
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param array $largeSize
     *
     * @return self
     */
    public function setLargeSize($largeSize)
    {
        $this->largeSize = $largeSize;

        return $this;
    }

    /**
     * @return array
     */
    public function getLargeSize()
    {
        return $this->largeSize;
    }

    /**
     * @param array $midSize
     *
     * @return self
     */
    public function setMidSize($midSize)
    {
        $this->midSize = $midSize;

        return $this;
    }

    /**
     * @return array
     */
    public function getMidSize()
    {
        return $this->midSize;
    }

    /**
     * @param array $smallSize
     *
     * @return self
     */
    public function setSmallSize($smallSize)
    {
        $this->smallSize = $smallSize;

        return $this;
    }

    /**
     * @return array
     */
    public function getSmallSize()
    {
        return $this->smallSize;
    }

    /**
     * @param array $thumbnailSize
     *
     * @return self
     */
    public function setThumbnailSize($thumbnailSize)
    {
        $this->thumbnailSize = $thumbnailSize;

        return $this;
    }

    /**
     * @return array
     */
    public function getThumbnailSize()
    {
        return $this->thumbnailSize;
    }


    
}