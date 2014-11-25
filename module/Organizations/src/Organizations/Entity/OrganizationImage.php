<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UserImage.php */ 
namespace Organizations\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Core\Entity\FileEntity;

/**
 * Defines the logo of an organiozation
 * 
 * @ODM\Document(collection="organization.images", repositoryClass="Organizations\Repository\OrganizationImage")
 */
class OrganizationImage extends FileEntity implements ResourceInterface
{

    /**
     * ImageUrl
     * 
     * @var string
     * @ODM\String
     */
    protected $imageUri;

    /**
     * @param $uri
     * @return $this
     */
    public function setImageUri($uri)
    {
        $this->imageUri = $uri;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageUri()
    {
        return $this->imageUri;
    }


    /**
     * {@inheritDoc}
     * @see \Zend\Permissions\Acl\Resource\ResourceInterface::getResourceId()
     */
    public function getResourceId()
    {
        return 'Entity/OrganizationImage';
    }
}
