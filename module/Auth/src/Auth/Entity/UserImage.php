<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** UserImage.php */ 
namespace Auth\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\FileEntity;

/**
 * @ODM\Document(collection="users.files")
 */
class UserImage extends FileEntity
{
    
}

