<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UserImage.php */
namespace Auth\Entity;

use Core\Entity\FileEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Defines a Profile Image of an user
 *
 * @ODM\HasLifecycleCallbacks()
 * @ODM\Document(collection="users.images")
 */
class UserImage extends FileEntity implements ResourceInterface
{
    /**
     * Gets the URI of an attachment
     *
     * @return string
     */
    function getUri()
    {
        return '/file/Auth.UserImage/' . $this->id;
    }

    /**
     * @ODM\PreRemove()
     */
    public function preRemove()
    {
        /* Auth\Entity\UserImage */
        $this->getUser()->getInfo()->setImage(null);
    }
}
