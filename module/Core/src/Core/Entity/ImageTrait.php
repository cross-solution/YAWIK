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
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
trait ImageTrait
{
    /**
     *
     * @ODM\Field(type="string")
     * @var string
     */
    protected $belongsTo;

    /**
     *
     * @ODM\Field
     * @var string
     */
    protected $key;

    public function setBelongsTo($imageSetId)
    {
        $this->belongsTo = $imageSetId;

        return $this;
    }

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