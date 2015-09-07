<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UserImage.php */
namespace Organizations\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Core\Entity\FileEntity;

/**
 * Defines the logo of an organiozation
 * @ODM\HasLifecycleCallbacks()
 * @ODM\Document(collection="organizations.images", repositoryClass="Organizations\Repository\OrganizationImage")
 */
class OrganizationImage extends FileEntity implements ResourceInterface
{
    /**
     * @var Organization
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\Organization", mappedBy="image")
     */
    protected $organization;

    /**
     * {@inheritDoc}
     * @see \Zend\Permissions\Acl\Resource\ResourceInterface::getResourceId()
     */
    public function getResourceId()
    {
        return 'Entity/OrganizationImage';
    }

    /**
     * get the URI of an attachment
     * @return string
     */
    function getUri()
    {
        return "/file/Organizations.OrganizationImage/" . $this->id . "/" . urlencode($this->name);
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @ODM\PreRemove()
     */
    public function preRemove()
    {
        $this->getOrganization()->setImage(null);
    }
}
