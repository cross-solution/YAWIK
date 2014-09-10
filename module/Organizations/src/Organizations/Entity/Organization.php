<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Repository\DoctrineMongoODM\Annotation as Cam;
use Doctrine\Common\Collections\Collection;
use Core\Entity\AddressInterface;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\EntityInterface;

/**
 * The job model
 *
 * @ODM\Document(collection="organizations", repositoryClass="Organizations\Repository\Organization")
 */
class Organization extends BaseEntity implements OrganizationInterface {
    
    
    const postConstruct = 'postRepositoryConstruct';
    
    /**
     * externalId. Allows external applications to reference their primary key.
     * 
     * @var string
     * @ODM\String
     */
    protected $externalId; 
    
    /**
     * The actual name of the organization.
     * 
     * @var \Organizations\Entity\OrganizationName
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationName", simple=true, cascade="persist")
     */
    protected $organizationName; 
    
    /**
     * primary logo of an organization
     * 
     * @var \Organizations\Entity\OrganizationImage
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationImage", cascade={"persist","update","remove"}, orphanRemoval=true, simple=true, nullable=true) 
     */
    protected $image;
    
    /**
     * Organization contact data.
     *
     * @ODM\EmbedOne(targetDocument="\Organizations\Entity\OrganizationContact") */
    protected $contact;

    /** {@inheritdoc} */
    public function setExternalId($externalId) 
    {
        $this->externalId = $externalId;
        return $this;
    }

    /** {@inheritdoc} */
    public function getExternalId() 
    {
        return $this->externalId;
    }

    /** {@inheritdoc} */
    public function setOrganizationName(OrganizationName $organizationName) 
    {
        if (isset($this->organizationName)) {
            $this->organizationName->refCounterDec()->refCompanyCounterDec();
        }
        $this->organizationName = $organizationName;
        $this->organizationName->refCounterInc()->refCompanyCounterInc();
        return $this;
    }

    /** {@inheritdoc} */
   public function getOrganizationName() 
   {
       return $this->organizationName;
   }

    /** {@inheritdoc} */
   public function setAddresses(AddressInterface $addresses)
   {
   }

    /** {@inheritdoc} */
   public function getAddresses() 
   {
   }

    /** {@inheritdoc} */
    public function getSearchableProperties()
    {
    }

    /** {@inheritdoc} */
    public function setKeywords(array $keywords)
    {
    }

    /** {@inheritdoc} */
    public function clearKeywords()
    {
    }

    /** {@inheritdoc} */
    public function getKeywords()
    {
    }

    /** {@inheritdoc} */
    public function getPermissions()
    {
    }

    /** {@inheritdoc} */
    public function setPermissions(PermissionsInterface $permissions) 
    {
        // @TODO: set Permissions
    }

    /** {@inheritdoc} */
    public function setImage(EntityInterface $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /** {@inheritdoc} */
    public function getImage()
    {
        return $this->image;
    }
    
    /** 
     * {@inheritdoc} 
     */
    public function setContact(EntityInterface $contact = null)
    {
        $this->contact = $contact;
        return $this;
    }

    /** 
     * {@inheritdoc} 
     */
    public function getContact()
    {
        return $this->contact;
    }
}


