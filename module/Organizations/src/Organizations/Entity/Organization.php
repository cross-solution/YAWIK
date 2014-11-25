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
use Core\Entity\AddressInterface;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Core\Entity\EntityInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\DraftableEntityInterface;

/**
 * The organization.
 *
 * @ODM\Document(collection="organizations", repositoryClass="Organizations\Repository\Organization")
 */
class Organization extends BaseEntity implements OrganizationInterface, DraftableEntityInterface {
    
    
    const postConstruct = 'postRepositoryConstruct';
    
    /**
     * externalId. Allows external applications to reference their primary key.
     * 
     * @var string
     * @ODM\String
     * @ODM\Index
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
     * Assigned permissions.
     *
     * @var PermissionsInterface
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Permissions")
     */
    protected $permissions;
    
    /**
     * primary logo of an organization
     * 
     * @var \Organizations\Entity\OrganizationImage
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationImage", cascade={"persist","update","remove"}, orphanRemoval=true, simple=true, nullable=true) 
     */
    protected $image;

    /**
     * Flag indicating draft state of this job.
     *
     * @var bool
     * @ODM\Boolean
     */
    protected $isDraft = false;
    
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

    /**
     * Set hydrator
     *
     * @param  HydratorInterface $hydrator
     * @return HydratorAwareInterface
     */
    public function setHydrator(HydratorInterface $hydrator) {
        return $this;
    }

    /**
     * Retrieve hydrator
     *
     * @return HydratorInterface
     */
    public function getHydrator() {
        return new EntityHydrator();
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

    /**
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsAwareInterface::getPermissions()
     */
    public function getPermissions()
    {
        if (!$this->permissions) {
            $permissions = new Permissions();
            //if ($this->user) {
            //    $permissions->grant($this->user, Permissions::PERMISSION_ALL);
            //}
            $this->setPermissions($permissions);
        }
        return $this->permissions;
    }

    /**
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsAwareInterface::setPermissions()
     * @return $this
     */
    public function setPermissions(PermissionsInterface $permissions) {
        $this->permissions = $permissions;
        return $this;
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
        if (!$contact instanceOf OrganizationContact) {
            $contact = new OrganizationContact($contact);
        }
        $this->contact = $contact;
        return $this;
    }

    /** 
     * {@inheritdoc} 
     */
    public function getContact()
    {
        if (!$this->contact instanceOf OrganizationContact) {
            $this->contact = new OrganizationContact();
        }
        return $this->contact;
    }

    /**
     * Gets the flag indicating the draft state.
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * Sets the flag indicating the draft state.
     *
     * @param boolean $flag
     * @return DraftableEntityInterface
     */
    public function setIsDraft($flag)
    {
        $this->isDraft = (bool) $flag;
        return $this;
    }
}


