<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Auth\Entity\UserInterface;
use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Core\Entity\Collection\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
 * @ODM\HasLifecycleCallbacks
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Organization extends BaseEntity implements OrganizationInterface, DraftableEntityInterface {
    
    
    const postConstruct = 'postRepositoryConstruct';

    /**
     * Owner of the organization
     *
     * @var \Auth\Entity\UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple="true")
     * @since 0.18
     */
    protected $owner;
    
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
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationImage", inversedBy="organization", simple=true, nullable="true", cascade={"all"})
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

    /**
     * @var string
     * @ODM\String
     */
    protected $description;

    /**
     * The parent of this organization.
     *
     * @see setParent()
     * @var OrganizationInterface | null
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\Organization", simple=true, nullable=true)
     * @since 0.18
     */
    protected $parent;

    /**
     * The hiring organizations of this organization.
     *
     * @var Collection
     * @ODM\ReferenceMany(
     *      targetDocument="Organizations\Entity\Organization",
     *      repositoryMethod="getHiringOrganizationsCursor"
     * )
     * @since 0.18
     */
    protected $hiringOrganizations;

    /**
     * The associated employees (users)
     *
     * @ODM\EmbedMany(targetDocument="\Organizations\Entity\Employee")
     * @var Collection
     */
    protected $employees;

    /**
     * the owner of a Organization
     *
     * @var UserInterface $user
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true)
     * @ODM\Index
     */
    protected $user;

    public function setOwner(UserInterface $user)
    {
        if ($this->owner) {
            $this->getPermissions()->revoke($this->owner, Permissions::PERMISSION_ALL, /*build*/ false);
        }
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);
        $this->owner = $user;

        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setParent(OrganizationInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getHiringOrganizations()
    {
        return $this->hiringOrganizations;
    }

    public function isHiringOrganization()
    {
        return null !== $this->parent;
    }

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
    public function setImage(OrganizationImage $image = null)
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setEmployees(Collection $employees)
    {
        /* todo: Throw exception or at least log incidents, where employees are added to "hiring orgs" */
        if (!$this->isHiringOrganization()) {
            $this->employees = $employees;
        }

        return $this;
    }

    public function getEmployees()
    {
        if ($this->isHiringOrganization()) {
            // Always return empty list, as we never have employees in this case.
            return new ArrayCollection();
        }

        if (!$this->employees) {
            $this->setEmployees(new ArrayCollection());
        }

        return $this->employees;
    }

    /**
     * Updates the organizationsPermissions to allow all employees to view this organization.
     *
     * In case of a HiringOrganization Permissions are granted to all employees of the parent
     * organization.
     *
     * @ODM\PreUpdate
     * @ODM\PrePersist
     * @since 0.18
     */
    public function updatePermissions()
    {
        if ($this->isHiringOrganization()) {
            $organization = $this->getParent();
            $owner        = $organization->getOwner();
            $this->setOwner($owner);
        } else {
            $organization = $this;
        }


        /* @var $employees null | ArrayCollection | \Doctrine\ODM\MongoDB\PersistentCollection */
        $employees = $organization->getEmployees();

        if ($employees &&
            ( $employees instanceof ArrayCollection
              || $employees->isDirty()
              || $employees->isInitialized())
        ) {
            /* @var $perms Permissions */
            $perms = $this->getPermissions();

            foreach ($employees as $emp) {
                /* @var $emp \Organizations\Entity\Employee */
                $perms->grant($emp->getUser(), PermissionsInterface::PERMISSION_CHANGE, false);
            }
            $perms->build();
        }

    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user) {
        if ($this->user) {
            $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL, false);
        }
        $this->user = $user;
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);
        return $this;
    }
    /**
     * @return UserInterface
     */
    public function getUser() {
        return $this->user;
    }
}



