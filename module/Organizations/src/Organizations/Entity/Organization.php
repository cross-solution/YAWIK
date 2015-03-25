<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
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

    /**
     * Event name of post construct event.
     *
     * @var string
     */
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
     * The organizations' description.
     *
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
     * Jobs of this organization.
     *
     * @var Collection
     * @ODM\ReferenceMany(targetDocument="\Jobs\Entity\Job", simple=true, mappedBy="organization")
     * @since 0.18
     */
    protected $jobs;

    /**
     * the owner of a Organization
     *
     * @var UserInterface $user
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true)
     * @ODM\Index
     */
    protected $user;

    /**
     * Internal references (database query optimization)
     *
     * @var InternalReferences
     * @ODM\EmbedOne(targetDocument="InternalReferences")
     */
    protected $refs;

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

    /**
     * Sets the external id.
     *
     * @todo Has to be in interface!
     * @param $externalId
     *
     * @return self
     */
    public function setExternalId($externalId) 
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * Gets the internal id.
     * @todo has to be in interface!
     *
     * @return string
     */
    public function getExternalId() 
    {
        return $this->externalId;
    }

    public function setHydrator(HydratorInterface $hydrator) {
        return $this;
    }

    public function getHydrator() {
        return new EntityHydrator();
    }

    public function setOrganizationName(OrganizationName $organizationName)
    {
        if (isset($this->organizationName)) {
            $this->organizationName->refCounterDec()->refCompanyCounterDec();
        }
        $this->organizationName = $organizationName;
        $this->organizationName->refCounterInc()->refCompanyCounterInc();
        return $this;
    }

    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    public function setAddresses(AddressInterface $addresses)
    { }

    public function getAddresses()
    { }

    public function getSearchableProperties()
    { }

    public function setKeywords(array $keywords)
    { }

    public function clearKeywords()
    { }

    public function getKeywords()
    { }

    public function getPermissions()
    {
        if (!$this->permissions) {
            $permissions = new Permissions();
            if ($this->user) {
                $permissions->grant($this->user, Permissions::PERMISSION_ALL);
            }
            $this->setPermissions($permissions);
        }
        return $this->permissions;
    }

    public function setPermissions(PermissionsInterface $permissions) {
        // Assure the user has always all rights.
        if ($this->user) {
            $permissions->grant($this->user, Permissions::PERMISSION_ALL);
        }
        $this->permissions = $permissions;
        return $this;
    }

    public function getPermissionsResourceId()
    {
        return 'organization:' . $this->getId();
    }

    public function getPermissionsUserIds($type = null)
    {
        // Grant Owner of organization full access
        $spec = array(PermissionsInterface::PERMISSION_ALL => array($this->getUser()->getId()));

        if (null === $type || ('Job/Permissions' != $type && 'Application' != $type)) {
            return $spec;
        }

        if ('Job/Permissions' == $type) {
            $change = EmployeePermissionsInterface::JOBS_CHANGE;
            $view = EmployeePermissionsInterface::JOBS_VIEW;
        } else {
            $change = EmployeePermissionsInterface::APPLICATIONS_CHANGE;
            $view = EmployeePermissionsInterface::APPLICATIONS_VIEW;
        }

        $employees = $this->getEmployees();

        foreach ($employees as $emp) {
            /* @var $emp EmployeeInterface */
            $perm = $emp->getPermissions();
            if ($perm->isAllowed($change)) {
                $spec[$change][] = $emp->getUser()->getId();
            } else if ($perm->isAllowed($view)) {
                $spec[$view][] = $emp->getUser()->getId();
            }
        }

        return $spec;
    }

    /**
     * Sets logo.
     *
     * @todo has to be in interface
     * @param OrganizationImage $image
     *
     * @return self
     */
    public function setImage(OrganizationImage $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * gets image
     *
     * @todo has to be in interface
     * @return OrganizationImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets contact.
     *
     * @todo has to be in interface
     * @param EntityInterface $contact
     *
     * @return self
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
     * gets the contact
     * @todo has to be in interface
     * @return OrganizationContact
     */
    public function getContact()
    {
        if (!$this->contact instanceOf OrganizationContact) {
            $this->contact = new OrganizationContact();
        }
        return $this->contact;
    }

    public function isDraft()
    {
        return $this->isDraft;
    }

    public function setIsDraft($flag)
    {
        $this->isDraft = (bool) $flag;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

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

    public function isOwner(UserInterface $user)
    {
        return $this->getUser()->getId() == $user->getId();
    }

    public function isEmployee(UserInterface $user)
    {
        return $this->refs && in_array($user->getId(), $this->refs->getEmployeeIds());
    }

    public function isAssociated(UserInterface $user)
    {
        return $this->isOwner($user) || $this->isEmployee($user);
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
            $owner        = $organization->getUser();

            $this->setUser($owner);
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

    public function getInternalReferences()
    {
        if (!$this->refs) {
            $this->refs = new InternalReferences();
            $this->refs->setEmployeesIdsFromCollection($this->getEmployees());
        }

        return $this->refs;
    }

    /**
     * Updates the internal references.
     *
     *
     * @ODM\PreUpdate
     * @ODM\PrePersist
     * @since 0.18
     */
    public function updateInternalReferences()
    {
        $this->getInternalReferences()
             ->setEmployeesIdsFromCollection($this->getEmployees());
    }

    public function setUser(UserInterface $user) {
        if ($this->user) {
            $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL, false);
        }
        $this->user = $user;
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    public function getJobs()
    {
        return $this->jobs;
    }
}



