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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface ImageSetInterface extends EntityInterface
{

    const ORIGINAL  = 'original';
    const THUMBNAIL = 'thumbnail';

    public function clear();

    public function setImagesCollection(Collection $images);

    public function setImages(array $images, PermissionsInterface $permissions = null);

    public function get($key);
    public function set($key, ImageInterface $image);

    public function setPermissions(PermissionsInterface $permissions);
}