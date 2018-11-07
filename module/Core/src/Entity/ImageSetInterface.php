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

use Doctrine\Common\Collections\Collection;

/**
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0,29
 */
interface ImageSetInterface extends EntityInterface
{

    /**#@+
     * Special image key.
     *
     * @var string
     */
    const ORIGINAL  = 'original';
    const THUMBNAIL = 'thumbnail';

    /**#@-*/

    /**
     * Clear this image set.
     *
     * @return self
     */
    public function clear();

    /**
     * Set the images collection
     *
     * @param Collection $images
     *
     * @return self
     */
    public function setImagesCollection(Collection $images);

    /**
     * Set images.
     *
     * Replaces the whole set.
     *
     * <pre>
     * $images = [
     *     <imageKey> => $image,
     * ]
     * </pre>
     *
     * @param array                $images
     * @param PermissionsInterface $permissions
     *
     * @return mixed
     */
    public function setImages(array $images, PermissionsInterface $permissions = null);

    /**
     * Get an image
     *
     * @param string $key
     *
     * @return ImageInterface
     */
    public function get($key);

    /**
     * Set an image.
     *
     * Any image with the same $key will be replaced.
     *
     * @param string         $key
     * @param ImageInterface $image
     *
     * @return self
     */
    public function set($key, ImageInterface $image);

    /**
     * Set permissions for all images in this set.
     *
     * @param PermissionsInterface $permissions
     *
     * @return self
     */
    public function setPermissions(PermissionsInterface $permissions);
}
