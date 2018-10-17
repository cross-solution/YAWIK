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
 * Implements \Core\Entity\ImageInterface
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
trait ImageTrait
{
    /**
     * Id of the image set.
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $belongsTo;

    /**
     * Key name in the set.
     *
     * @ODM\Field
     * @var string
     */
    protected $key;

    /**
     * @param string $imageSetId
     *
     * @return self
     */
    public function setBelongsTo($imageSetId)
    {
        $this->belongsTo = $imageSetId;

        return $this;
    }

    /**
     * @return string
     */
    public function belongsTo()
    {
        return $this->belongsTo;
    }

    /**
     * @param string $key
     *
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
