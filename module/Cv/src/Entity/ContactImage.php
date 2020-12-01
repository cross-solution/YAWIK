<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Cv\Entity;

use Core\Entity\Image;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\File(bucketName="cvs.fs.contact.images")
 */
class ContactImage extends Image
{
    /**
     * Gets the URI of an image
     *
     * The returned URI is NOT prepended with the base path!
     *
     * @return string
     */
    public function getUri(): string
    {
        return "/file/Cv.ContactImage/" . $this->id . "/" .urlencode($this->name);
    }
}
