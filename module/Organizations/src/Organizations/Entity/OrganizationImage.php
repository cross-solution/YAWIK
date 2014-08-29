<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UserImage.php */ 
namespace Organizations\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\FileEntity;

/**
 * Defines a Organization Image of an user
 * 
 * @ODM\Document(collection="organization.images", repositoryClass="Organizations\Repository\OrganizationImage")
 */
class OrganizationImage extends FileEntity
{

    /**
     * ImageUrl
     * 
     * @var string
     * @ODM\String
     */
    protected $imageUri;

    public function setImageUri($uri)
    {
        $this->imageUri = $uri;
        return $this;
    }
    
    public function getImageUri()
    {
        return $this->imageUri;
    }
}
