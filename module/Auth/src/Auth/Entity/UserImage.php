<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UserImage.php */
namespace Auth\Entity;

use Core\Entity\Image;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Defines a Profile Image of an user
 *
 * @ODM\HasLifecycleCallbacks()
 * @ODM\File(bucketName="users.images")
 */
class UserImage extends Image
{
    /**
     * Gets the URI of an attachment
     *
     * @return string
     */
    public function getUri(): string
    {
        return '/file/Auth.UserImage/' . $this->id;
    }

    /**
     * @ODM\PreRemove()
     */
    public function preRemove()
    {
        /* Auth\Entity\UserImage */
        $this->getMetadata()->getUser()->getInfo()->setImage(null);
    }
}
