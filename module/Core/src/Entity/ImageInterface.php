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

/**
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
interface ImageInterface extends FileInterface
{

    /**
     * Set the id of the image set this image belongs to.
     *
     * @param string $imageSetId
     *
     * @return self
     */
    public function setBelongsTo($imageSetId);

    /**
     * Get the id of the image set this image belongs to.
     *
     * @return string
     */
    public function belongsTo();

    /**
     * Set image key.
     *
     * @param string $key
     *
     * @return self
     */
    public function setKey($key);

    /**
     * Get image key.
     *
     * @return string
     */
    public function getKey();
}
