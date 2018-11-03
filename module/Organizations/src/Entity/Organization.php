<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Organizations\Entity;

use Auth\Entity\UserInterface;
use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\DraftableEntityInterface;
use Core\Entity\EntityInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\ImageSet;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Hydrator\HydratorInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * The organization.
 *
 * @ODM\Document(collection="organizations", repositoryClass="Organizations\Repository\Organization")
 * @ODM\HasLifecycleCallbacks
 * @ODM\Indexes({
 *      @ODM\Index(keys={
 *          "_organizationName"="text"
 *      }, name="fulltext")
 * })
 *
 * @todo   write test
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class Organization extends BaseEntity implements
    OrganizationInterface,
    DraftableEntityInterface,
    ResourceInterface
{

    /**
     * Always enabled even if there are no active jobs
     */
    const PROFILE_ALWAYS_ENABLE     = 'always';

    /**
     * Hide if there are no jobs available
     */
    const PROFILE_ACTIVE_JOBS       = 'active-jobs';

    /**
     * Always disabled profile
     */
    const PROFILE_DISABLED          = 'disabled';

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
     * @ODM\Field(type="string")
     * @ODM\Index
     */
    protected $externalId;

    /**
     * The actual name of the organization.
     *
     * @var \Organizations\Entity\OrganizationName
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationName", storeAs="id", cascade="persist")
     */
    protected $organizationName;

    /**
     * Only for sorting/searching purposes
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $_organizationName;

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
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationImage", inversedBy="organization", storeAs="id", nullable="true", cascade={"all"})
     */
    protected $image;

    /**
     *
     *
     * @ODM\EmbedOne(targetDocument="\Core\Entity\ImageSet")
     * @var ImageSet
     */
    protected $images;

    /**
     * Flag indicating draft state of this job.
     *
     * @var bool
     * @ODM\Field(type="boolean")
     */
    protected $isDraft = false;

    /**
     * Organization contact data.
     *
     * @ODM\EmbedOne(targetDocument="\Organizations\Entity\OrganizationContact")
     */
    protected $contact;

    /**
     * The organizations' description.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $description;

    /**
     * The parent of this organization.
     *
     * @see   setParent()
     * @var OrganizationInterface | null
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\Organization", storeAs="id", nullable=true)
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
     * @ODM\ReferenceMany(targetDocument="\Jobs\Entity\Job", storeAs="id", mappedBy="organization")
     * @since 0.18
     */
    protected $jobs;

    /**
     * the owner of a Organization
     *
     * @var UserInterface $user
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", storeAs="id")
     * @ODM\Index
     */
    protected $user;

    /**
     * Default values of an organizations job template
     *
     * @var TemplateInterface;
     * @ODM\EmbedOne(targetDocument="\Organizations\Entity\Template")
     */
    protected $template;

    /**
     * Default values Workflow
     *
     * @var WorkflowSettingsInterface $workflowSettings ;
     * @ODM\EmbedOne(targetDocument="\Organizations\Entity\WorkflowSettings")
     */
    protected $workflowSettings;

    /**
     * Profile Setting
     * @var string
     * @ODM\Field(type="string", nullable=true)
     */
    protected $profileSetting;

    /**
     * @return string
     */
    public function getProfileSetting()
    {
        return $this->profileSetting;
    }

    /**
     * @param string $profileSetting
     *
     * @return $this
     */
    public function setProfileSetting($profileSetting)
    {
        $this->profileSetting = $profileSetting;

        return $this;
    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Entity/Organization';
    }

    /**
     * Gets the organization name
     *
     * @return string
     */
    public function getName()
    {
        if (empty($this->organizationName)) {
            return '';
        }

        return $this->organizationName->getName();
    }

    /**
     * Sets the parent of an organization
     *
     * @param OrganizationInterface $parent
     *
     * @return $this
     */
    public function setParent(OrganizationInterface $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @deprecated
     * @return array
     */
    public function getSearchableProperties()
    {
        return array();
    }

    /**
     * Gets the parent of an organization
     *
     * @param bool $returnSelf returns itself, if this organization does not have a parent?
     *
     * @return null|OrganizationInterface
     */
    public function getParent($returnSelf = false)
    {
        return $this->parent ? : ($returnSelf ? $this : null);
    }

    /**
     * Gets the Draft flag
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * Gets linked organizations
     *
     * @return Collection
     */
    public function getHiringOrganizations()
    {
        return $this->hiringOrganizations;
    }

    /**
     * Sets the draft flag
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function setIsDraft($flag)
    {
        $this->isDraft = (bool) $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHiringOrganization()
    {
        return null !== $this->parent;
    }

    /**
     * Returns true, if the user belongs to the organization.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isAssociated(UserInterface $user)
    {
        return $this->isOwner($user) || $this->isEmployee($user);
    }

    /**
     * Sets the external id.
     *
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
     * Checks, if a User is the owner of an organization
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isOwner(UserInterface $user)
    {
        return $this->getUser()->getId() == $user->getId();
    }

    /**
     * Gets the internal id.
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Returns true, if a User is an employee of the organization
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEmployee(UserInterface $user)
    {
        return $this->refs && in_array($user->getId(), $this->refs->getEmployeeIds());
    }

    /**
     * @todo review this
     *
     * @param HydratorInterface $hydrator
     *
     * @return $this
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        return $this;
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

        $perms = $this->getPermissions();

        foreach ($employees as $emp) {
            /* @var $emp \Organizations\Entity\Employee */
            $perms->grant($emp->getUser(), PermissionsInterface::PERMISSION_CHANGE, false);
        }
        $perms->build();
    }

    /**
     * * @todo review this
     * @return EntityHydrator
     */
    public function getHydrator()
    {
        return new EntityHydrator();
    }

    /**
     * Sets the name of an organization
     *
     * @param OrganizationName $organizationName
     *
     * @return $this
     */
    public function setOrganizationName(OrganizationName $organizationName)
    {
        if (isset($this->organizationName)) {
            $this->organizationName->refCounterDec()->refCompanyCounterDec();
        }
        $this->organizationName = $organizationName;
        $this->organizationName->refCounterInc()->refCompanyCounterInc();
        $this->_organizationName = $organizationName->getName();

        return $this;
    }

    /**
     * Gets the organization name entity of an organisation
     *
     * @return OrganizationName
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * Gets the Permissions of an organization
     *
     * @return PermissionsInterface
     */
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

    /**
     * Sets the Permissions of an Organization
     *
     * @param PermissionsInterface $permissions
     *
     * @return $this
     */
    public function setPermissions(PermissionsInterface $permissions)
    {
        // Assure the user has always all rights.
        if ($this->user) {
            $permissions->grant($this->user, Permissions::PERMISSION_ALL);
        }
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Gets the Permissions Resource ID
     *
     * @return string
     */
    public function getPermissionsResourceId()
    {
        return 'organization:' . $this->getId();
    }

    /**
     * @param null $type
     *
     * @return array
     */
    public function getPermissionsUserIds($type = null)
    {
        // if we have a user, grant him full access to all associated permissions.
        $user = $this->getUser();
        $spec = $user
            ? $spec = array(PermissionsInterface::PERMISSION_ALL => array($this->getUser()->getId()))
            : array();

        if (null === $type || ('Job/Permissions' != $type && 'Application' != $type)) {
            return $spec;
        }

        if ('Job/Permissions' == $type) {
            $change = EmployeePermissionsInterface::JOBS_CHANGE;
            $view   = EmployeePermissionsInterface::JOBS_VIEW;
        } else {
            $change = EmployeePermissionsInterface::APPLICATIONS_CHANGE;
            $view   = EmployeePermissionsInterface::APPLICATIONS_VIEW;
        }

        $employees = $this->isHiringOrganization()
            ? $this->getParent()->getEmployees()
            : $this->getEmployees();

        foreach ($employees as $emp) {
            /* @var $emp EmployeeInterface */
            if ($emp->isUnassigned()) {
                continue;
            }

            $perm = $emp->getPermissions();
            if ($perm->isAllowed($change)) {
                $spec[PermissionsInterface::PERMISSION_CHANGE][] = $emp->getUser()->getId();
            } elseif ($perm->isAllowed($view)) {
                $spec[PermissionsInterface::PERMISSION_VIEW][] = $emp->getUser()->getId();
            }
        }

        return $spec;
    }

    /**
     * Sets the logo of an organization
     *
     * @param OrganizationImage $image
     *
     * @return self
     * @deprecated since 0.29; use $this->getImages()->set()
     */
    public function setImage(OrganizationImage $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Gets the Logo of an organization
     *
     * @param string|bool $key Key of the image to get.
     *                         If true: get Thumbnail
     *                         If false: get Original
     *
     * @return OrganizationImage
     * @deprecated since 0.29; use $this->getImages()->get()
     * @since 0.29 modified to return images from the image set for compatibility reasons
     */
    public function getImage($key = ImageSet::ORIGINAL)
    {
        if (is_bool($key)) {
            $key = $key ? ImageSet::THUMBNAIL : ImageSet::ORIGINAL;
        }

        return $this->getImages()->get($key, false) ?: $this->image;
    }

    /**
     * @param ImageSet $images
     *
     * @return self
     */
    public function setImages(ImageSet $images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return ImageSet
     */
    public function getImages()
    {
        if (!$this->images) {
            $this->images = new ImageSet();
        }

        return $this->images;
    }

    /**
     *
     *
     * @return self
     */
    public function removeImages()
    {
        $this->images = null;

        return $this;
    }

    /**
     * Sets the Contact Data of an organization
     *
     * @param EntityInterface $contact
     *
     * @return $this
     */
    public function setContact(EntityInterface $contact = null)
    {
        if (!$contact instanceof OrganizationContact) {
            $contact = new OrganizationContact($contact);
        }
        $this->contact = $contact;

        return $this;
    }

    /**
     * Gets the contact Data of an organization
     *
     * @return OrganizationContact
     */
    public function getContact()
    {
        if (!$this->contact instanceof OrganizationContact) {
            $this->contact = new OrganizationContact();
        }

        return $this->contact;
    }

    /**
     * Gets the default description of an organization.
     *
     * This description is used as the default of the company_description
     * used in a job template
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the default description af an organization
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Sets the the list of employees
     *
     * @param Collection $employees
     *
     * @return $this
     */
    public function setEmployees(Collection $employees)
    {
        /* todo: Throw exception or at least log incidents, where employees are added to "hiring orgs" */
        if (!$this->isHiringOrganization()) {
            $this->employees = $employees;
        }

        return $this;
    }

    /**
     * Gets the list of employees
     *
     * @return ArrayCollection|Collection
     */
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
     * Gets an employee by User or ID.
     *
     * @param UserInterface|string $userOrId
     *
     * @return mixed|null
     */
    public function getEmployee($userOrId)
    {
        $employees = $this->getEmployees();
        $userId    = $userOrId instanceof \Auth\Entity\UserInterface ? $userOrId->getId() : $userOrId;

        foreach ($employees as $employee) {
            if ($employee->getUser()->getId() == $userId) {
                return $employee;
            }
        }

        return null;
    }

    /**
     * Gets a list of Employees by a user role
     *
     * @param string $role
     *
     * @return ArrayCollection
     */
    public function getEmployeesByRole($role)
    {
        $employees = new ArrayCollection();

        /* @var \Organizations\Entity\Employee $employee */
        foreach ($this->getEmployees() as $employee) {
            if ($role === $employee->getRole()) {
                $employees->add($employee);
            }
        }

        return $employees;
    }

    public function setUser(UserInterface $user)
    {
        if ($this->user) {
            $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL, false);
        }
        $this->user = $user;
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);

        return $this;
    }

    /**
     * Gets the owner of the organization
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Gets the Jobs of an organization
     *
     * @return Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Gets default values of an organizations job template
     *
     * @return TemplateInterface
     */
    public function getTemplate()
    {
        if (null === $this->template) {
            $this->template = new Template();
        }

        return $this->template;
    }

    /**
     * Sets default values of an organizations job template
     *
     * @return self
     */
    public function setTemplate(TemplateInterface $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Gets Workflow Settings
     *
     * @return WorkflowSettings|WorkflowSettingsInterface
     */
    public function getWorkflowSettings()
    {
        if (null == $this->workflowSettings) {
            $this->workflowSettings = new WorkflowSettings();
        }

        return $this->workflowSettings;
    }

    /**
     * Sets Workflow Settings
     *
     * @param $workflowSettings
     *
     * @return self
     */
    public function setWorkflowSettings($workflowSettings)
    {
        $this->workflowSettings = $workflowSettings;

        return $this;
    }
}
