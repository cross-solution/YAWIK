<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** UserImage.php */ 
namespace Auth\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\FileEntity;

/**
 * Defines a Profile Image of an user
 * 
 * @ODM\Document(collection="users.images")
 */
class UserImage extends FileEntity
{
    protected $uri;
    
    /**
     * get the URI of an attachment
     * @return string
     */
    function getUri(){
        return '/file/Auth.UserImage/' . $this->id;
    }
}

