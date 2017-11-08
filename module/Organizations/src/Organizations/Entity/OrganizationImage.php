<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UserImage.php */
namespace Organizations\Entity;

use Core\Entity\ImageInterface;
use Core\Entity\ImageTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Core\Entity\FileEntity;

/**
 * Defines the logo of an organization.
 *
 * @ODM\Document(collection="organizations.images", repositoryClass="Organizations\Repository\OrganizationImage")
 */
class OrganizationImage extends FileEntity implements ImageInterface
{
    use ImageTrait;

    /**
     * Organization which belongs to the company logo
     *
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
     * Gets the URI of an attachment
     *
     * @return string
     */
    function getUri()
    {
        return '/' . trim('file/Organizations.OrganizationImage/' . $this->id . "/" . urlencode($this->name),'/');
    }

    /**
     * Gets the organization of an company logo
     *
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
}
