<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Entity;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Core\Entity\ClonePropertiesTrait;
use Core\Entity\AttachableEntityTrait;
use Core\Entity\EntityInterface;
use Core\Entity\MetaDataProviderTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\Collection;
use Auth\Entity\UserInterface;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Organizations\Entity\OrganizationInterface;
use Core\Entity\DraftableEntityInterface;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\SnapshotGeneratorProviderInterface;
use Zend\I18n\Validator\DateTime;

/**
 * The job model
 *
 * @ODM\Document(collection="jobs", repositoryClass="Jobs\Repository\Job")
 * @ODM\Indexes({
 *     @ODM\Index(keys={"datePublishStart.date"="asc"})
 * })
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @since 0.29 add temporary isDeleted flag and corresponding delete() method.
 */
class Job extends BaseEntity implements
    JobInterface,
                                        DraftableEntityInterface,
                                        SnapshotGeneratorProviderInterface
{
    use AttachableEntityTrait, MetaDataProviderTrait, ClonePropertiesTrait;


    private $cloneProperties = [
        'classifications', 'atsMode', 'salary',
    ];

    /**
     * unique ID of a job posting used by applications to reference
     * a job
     *
     * @var String
     * @ODM\Field(type="string") @ODM\Index
     **/
    protected $applyId;
    
    /**
     * title of a job posting
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $title;
    

    /**
     * name of the publishing company
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $company;
    
    /**
     * publishing company
     *
     * @var OrganizationInterface
     * @ODM\ReferenceOne (targetDocument="\Organizations\Entity\Organization", storeAs="id", inversedBy="jobs")
     * @ODM\Index
     */
    protected $organization;
    
    
    /**
     * Email Address, which is used to send notifications about e.g. new applications.
     *
     * @var String
     * @ODM\Field(type="string")
     **/
    protected $contactEmail;
    
    /**
     * the owner of a Job Posting
     *
     * @var UserInterface $user
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", storeAs="id")
     * @ODM\Index
     */
    protected $user;
    
    /**
     * all applications of a certain jobad
     *
     * @var Collection
     * @ODM\ReferenceMany(targetDocument="Applications\Entity\Application", storeAs="id", mappedBy="job",
     *                    repositoryMethod="loadApplicationsForJob")
     */
    protected $applications;
    
    /**
     * new applications
     *
     * @ODM\ReferenceMany(targetDocument="Applications\Entity\Application",
     *                    repositoryMethod="loadUnreadApplicationsForJob", mappedBy="job")
     * @var Int
     */
    protected $unreadApplications;
    
    /**
     * language of the job posting. Languages are ISO 639-1 coded
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $language;
    
    /**
     * location of the job posting. This is a plain text, which describes the location in
     * search e.g. results.
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $location;

    /**
     * locations of the job posting. This collection contains structured coordinates,
     * postal codes, city, region, and country names
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="Location")
     */
    protected $locations;
    
    /**
     * Link which points to the job posting
     *
     * @var String
     * @ODM\Field(type="string")
     **/
    protected $link;
    
    /**
     * publishing date of a job posting
     *
     * @var String
     * @ODM\Field(type="tz_date")
     */
    protected $datePublishStart;

    /**
     * end date of a job posting
     *
     * @var String
     * @ODM\Field(type="tz_date")
     */
    protected $datePublishEnd;
    
    /**
     * Status of the job posting
     *
     * @var Status
     * @ODM\EmbedOne(targetDocument="Status")
     * @ODM\Index
     */
    protected $status;

    /**
     * History on an job posting
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="History")
     */
    protected $history;

    /**
     * Flag, privacy policy is accepted or not.
     *
     * @var bool
     * @ODM\Field(type="boolean")
     */
    protected $termsAccepted;
    
    /**
     * Reference of a job opening, on which an applicant can refer to.
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $reference;
    
    /**
     * Unified Resource Locator to the company-Logo
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $logoRef;

    /**
     * Template-Name
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $template;

    /**
     * Application link.
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $uriApply;

    /**
     * Unified Resource Locator the Yawik, which handled this job first - so
     * does know who is the one who has commited this job.
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $uriPublisher;

    /**
     * @var
     * @ODM\EmbedMany(targetDocument="Publisher")
     */
    protected $publisher;

    /**
     * The ATS mode entity.
     *
     * @var AtsMode
     * @ODM\EmbedOne(targetDocument="AtsMode")
     */
    protected $atsMode;

    /**
     * this must be enabled to use applications forms etc. for this job or
     * to see number of applications in the list of applications
     *
     * @var Boolean
     *
     * @ODM\Field(type="boolean")
     */
    protected $atsEnabled;

    /**
     * The Salary entity.
     *
     * @var Salary
     * @ODM\EmbedOne(targetDocument="\Jobs\Entity\Salary")
     */
    protected $salary;

    /**
     * Permissions
     *
     * @var PermissionsInterface
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Permissions")
     */
    protected $permissions;

    /**
     * The actual name of the organization.
     *
     * @var TemplateValues
     * @ODM\EmbedOne(targetDocument="\Jobs\Entity\TemplateValues")
     */
    protected $templateValues;


    /**
     * Can contain various Portals
     *
     * @var array
     * @ODM\Field(type="collection")
     */
    protected $portals = array();

    /**
     * Flag indicating draft state of this job.
     *
     * @var bool
     * @ODM\Field(type="boolean")
     */
    protected $isDraft = false;

    /**
     * Classifications
     *
     * @ODM\EmbedOne(targetDocument="\Jobs\Entity\Classifications")
     * @var Classifications
     * @since 0.29
     */
    protected $classifications;

    /**
     * Delete flag.
     *
     * @internal
     *      This is meant as a temporary flag, until
     *      SoftDelete is implemented.
     *
     * @ODM\Field(type="boolean")
     * @var bool
     * @since 0.29
     */
    protected $isDeleted = false;

    /**
     *
     * @ODM\ReferenceMany(targetDocument="\Jobs\Entity\JobSnapshot", mappedBy="snapshotEntity", sort={"snapshotMeta.dateCreated"="desc"})
     * @var JobSnapshot[]
     */
    protected $snapshots;

    /**
     * @ODM\ReferenceOne(targetDocument="\Jobs\Entity\JobSnapshot", mappedBy="snapshotEntity", sort={"snapshotMeta.dateCreated"="desc"})
     *
     * @var JobSnapshot
     */
    protected $latestSnapshot;


    public function getSnapshots()
    {
        return $this->snapshots;
    }

    public function getLatestSnapshot()
    {
        return $this->latestSnapshot;
    }

    public function hasSnapshotDraft()
    {
        $snapshot = $this->getLatestSnapshot();
        return $snapshot && $snapshot->getSnapshotMeta()->hasStatus(JobSnapshotStatus::ACTIVE);
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return 'Entity/Jobs/Job';
    }

    /**
     * @see \Jobs\Entity\JobInterface::setApplyId()
     * @param String $applyId
     * @return \Jobs\Entity\JobInterface
     */
    public function setApplyId($applyId)
    {
        $this->applyId = (string) $applyId;
        return $this;
    }
    /**
     * @see \Jobs\Entity\JobInterface::getApplyId()
     * @return String
     */
    public function getApplyId()
    {
        if (!isset($this->applyId)) {
            // take the entity-id as a default
            $this->applyId = $this->id;
        }
        return $this->applyId;
    }

    /**
     * Gets the title of a job posting
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title of a job posting
     *
     * @see \Jobs\Entity\JobInterface::setTitle()
     * @param String $title
     * @return \Jobs\Entity\JobInterface
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
        return $this;
    }
    
    /**
     * Gets the name oof the company. If there is an organization assigned to the
     * job posting. Take the name of the organization.
     *
     * @param bool $useOrganizationEntity Get the name from the organization entity, if it is available.
     * @see \Jobs\Entity\JobInterface::getCompany()
     * @return string
     */
    public function getCompany($useOrganizationEntity = true)
    {
        if ($this->organization && $useOrganizationEntity) {
            return $this->organization->getOrganizationName()->getName();
        }

        return $this->company;
    }

    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setCompany()
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }
    
    /**
    * (non-PHPdoc)
    * @see \Jobs\Entity\JobInterface::getOrganization()
    */
    public function getOrganization()
    {
        return $this->organization;
    }
    
    /**
     * @inheritdoc
     */
    public function setOrganization(OrganizationInterface $organization = null)
    {
        $permissions = $this->getPermissions();

        if ($this->organization) {
            $permissions->revoke($this->organization, null, false);
        }
        $this->organization = $organization;
        $permissions->grant($organization);

        return $this;
    }



    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getContactEmail()
     */
    public function getContactEmail()
    {
        if (false !== $this->contactEmail && !$this->contactEmail) {
            $user = $this->getUser();
            $email = false;
            if (isset($user)) {
                $email = $user->getInfo()->getEmail();
            }
            $this->setContactEmail($email);
        }
        return $this->contactEmail;
    }

    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setContactEmail()
     */
    public function setContactEmail($email)
    {
        $this->contactEmail = (string) $email;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setLanguage()
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getLanguage()
     */
    public function getLanguage()
    {
        return $this->language;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setLocation()
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getLocation()
     */
    public function getLocation()
    {
        if (null === $this->location) {
            $array=[];
            if (null != $this->locations) {
                foreach ($this->locations as $location) { /* @var \Core\Entity\LocationInterface $location */
                    $array[]=(string) $location;
                }
                return implode(', ', $array);
            }
        }
        return $this->location;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setLocations()
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getLocations()
     */
    public function getLocations()
    {
        if (!$this->locations) {
            $this->setLocations(new ArrayCollection());
        }
        return $this->locations;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setUser()
     */
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
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getUser()
     */
    public function getUser()
    {
        return $this->user;
    }

    public function unsetUser($removePermissions = true)
    {
        if ($this->user) {
            if ($removePermissions) {
                $this->getPermissions()->revoke($this->user, Permissions::PERMISSION_ALL);
            }
            $this->user=null;
        }

        return $this;
    }

    public function unsetOrganization($removePermissions = true)
    {
        if ($this->organization && $removePermissions) {
            $this->getPermissions()->revoke($this->organization, Permissions::PERMISSION_ALL);
        }

        $this->organization = null;

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setApplications()
     */
    public function setApplications(Collection $applications)
    {
        $this->applications = $applications;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getApplications()
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Gets the number of unread applications
     * @return Collection
     */
    public function getUnreadApplications()
    {
        return $this->unreadApplications;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getLink()
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setLink()
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getDatePublishStart()
     */
    public function getDatePublishStart()
    {
        return $this->datePublishStart;
    }
    /**
     * (non-PHPdoc)
     * @param string $datePublishStart
     * @see \Jobs\Entity\JobInterface::setDatePublishStart()
     * @return $this
     */
    public function setDatePublishStart($datePublishStart = null)
    {
        if (!isset($datePublishStart) || is_string($datePublishStart)) {
            $datePublishStart = new \DateTime($datePublishStart);
        } elseif (!$datePublishStart instanceof \DateTime) {
            throw new \InvalidArgumentException('Expected object of type \DateTime');
        }

        $this->datePublishStart = $datePublishStart;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getDatePublishStart()
     */
    public function getDatePublishEnd()
    {
        return $this->datePublishEnd;
    }
    /**
     * (non-PHPdoc)
     * @param string $datePublishEnd
     * @see \Jobs\Entity\JobInterface::setDatePublishEnd()
     * @return $this
     */
    public function setDatePublishEnd($datePublishEnd = null)
    {
        if (is_string($datePublishEnd)) {
            $datePublishEnd = new \DateTime($datePublishEnd);
        } elseif (!$datePublishEnd instanceof \DateTime) {
            throw new \InvalidArgumentException('Expected object of type \DateTime');
        }

        $this->datePublishEnd = $datePublishEnd;
        return $this;
    }

    /**
     * Modifies the state of an application.
     *
     * Creates a history entry.
     *
     * @param StatusInterface|string $status
     * @param string $message
     * @return Job
     */
    public function changeStatus($status, $message = '[System]')
    {
        $this->setStatus($status);
        $status = $this->getStatus(); // ensure StatusEntity

        $history = new History($status, $message);

        $this->getHistory()->add($history);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getStatus()
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setStatus()
     */
    public function setStatus($status)
    {
        if (!$status instanceof Status) {
            $status = new Status($status);
        }
        $this->status = $status;
    }

    /**
     * {@inheritDoc}
     * @see JobInterface::setHistory()
     * @return Job
     */
    public function setHistory(Collection $history)
    {
        $this->history = $history;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see JobInterface::getHistory()
     */
    public function getHistory()
    {
        if (null == $this->history) {
            $this->setHistory(new ArrayCollection());
        }
        return $this->history;
    }

    /**
     * {@inheritDoc}
     * @see JobInterface::setTermsAccepted()
     * @return self
     */
    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = $termsAccepted;
        return $this;
    }

    /**
     * {qinheritDoc}
     * @see JobInterface::getTermsAccepted()
     */
    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    /**
     * (non-PHPdoc)
     * @see JobInterface::getReference()
     */
    public function getReference()
    {
        return $this->reference;
    }
    /**
     * (non-PHPdoc)
     * @see JobInterface::setReference()
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    public function setAtsMode(AtsMode $mode)
    {
        $this->atsMode = $mode;

        return $this;
    }

    public function getAtsMode()
    {
        if (!$this->atsMode) {
            $this->setAtsMode(new AtsMode(AtsMode::MODE_INTERN));
        }

        return $this->atsMode;
    }


    /**
     * checks, weather a job is enabled for getting applications
     * @return boolean
     */
    public function getAtsEnabled()
    {
        return $this->atsEnabled;
    }
    /**
     * enables a job add to receive applications
     *
     * @param boolean $atsEnabled
     * @return \Jobs\Entity\Job
     */
    public function setAtsEnabled($atsEnabled)
    {
        $this->atsEnabled = $atsEnabled;
        return $this;
    }

    /**
     * @param \Jobs\Entity\Salary $salary
     *
     * @return self
     */
    public function setSalary(Salary $salary)
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * @return \Jobs\Entity\Salary
     */
    public function getSalary()
    {
        if (!$this->salary) {
            $this->setSalary(new Salary());
        }

        return $this->salary;
    }

    /**
     * returns an uri to the organization logo.
     *
     * @return string
     */
    public function getLogoRef()
    {
        /** @var $organization \Organizations\Entity\Organization */
        $organization = $this->organization;
        if (is_object($organization) && $organization->getImage()) {
            $organizationImage = $organization->getImage();
            return "/file/Organizations.OrganizationImage/" . $organizationImage->getId();
        }
        return $this->logoRef;
    }
    /**
     * Set the uri to the organisations logo
     *
     * @param string $logoRef
     * @return \Jobs\Entity\Job
     */
    public function setLogoRef($logoRef)
    {
        $this->logoRef = $logoRef;
        return $this;
    }

    /**
     *
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = $this->template;
        if (empty($template)) {
            $template = 'default';
        }
        return $template;
    }
    /**
     *
     *
     * @param string $template name of the Template
     * @return \Jobs\Entity\Job
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }



    /**
     * Gets the uri of an application link
     *
     * @return String
     */
    public function getUriApply()
    {
        return $this->uriApply;
    }

    /**
     * Sets the uri of an application link
     *
     * @param String $uriApply
     * @return \Jobs\Entity\Job
     */
    public function setUriApply($uriApply)
    {
        $this->uriApply = $uriApply;
        return $this;
    }

    /**
     * Gets the uri of a publisher
     *
     * @return String
     */
    public function getUriPublisher()
    {
        return $this->uriPublisher;
    }
    /**
     *
     * @param String $uriPublisher
     * @return \Jobs\Entity\Job
     */
    public function setUriPublisher($uriPublisher)
    {
        $this->uriPublisher = $uriPublisher;
        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getPublisher($key)
    {
        $result = null;
        foreach ($this->publisher as $publisher) {
            if ($publisher->host == $key) {
                $result = $publisher;
            }
        }
        if (!isset($result)) {
            $result = new Publisher();
            $result->host = $key;
            $this->publisher[] = $result;
        }
        return $result;
    }

    public function setPublisherReference($key, $reference)
    {
        $publisher = $this->getPublisher($key);
        $publisher->reference;
        return $this;
    }

    
    /**
     * (non-PHPdoc)
     * @see \Core\Entity\PermissionsAwareInterface::getPermissions()
     */
    public function getPermissions()
    {
        if (!$this->permissions) {
            $permissions = new Permissions('Job/Permissions');
            if ($this->user) {
                $permissions->grant($this->user, Permissions::PERMISSION_ALL);
            }
            $this->setPermissions($permissions);
        }

        return $this->permissions;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Core\Entity\PermissionsAwareInterface::setPermissions()
     */
    public function setPermissions(PermissionsInterface $permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * Gets the Values of a job template
     *
     * @return TemplateValues
     */
    public function getTemplateValues()
    {
        if (!$this->templateValues instanceof TemplateValues) {
            $this->templateValues = new TemplateValues();
        }
        return $this->templateValues;
    }

    /**
     * @param EntityInterface $templateValues
     *
     * @return $this
     */
    public function setTemplateValues(EntityInterface $templateValues = null)
    {
        if (!$templateValues instanceof TemplateValues) {
            $templateValues = new TemplateValues($templateValues);
        }
        $this->templateValues = $templateValues;
        return $this;
    }

    /**
     * Sets the list of channels where a job opening should be published
     *
     * @param Array
     * {@inheritdoc}
     */
    public function setPortals(array $portals)
    {
        $this->portals = $portals;
        return $this;
    }

    /**
     * Gets the list of channels where the job opening should be published
     *
     * {@inheritdoc}
     * @return array
     */
    public function getPortals()
    {
        return $this->portals;
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
     * Gets the status and checks it against 'active'
     *
     * @return bool
     */
    public function isActive()
    {
        return !$this->isDraft && is_object($this->status) && $this->status->getName() == 'active';
    }

    /**
     * @return Job
     */
    public function makeSnapshot()
    {
        $snapshot = new JobSnapshot($this);
        return $snapshot;
    }

    /**
     * @return array|mixed
     */
    public function getSnapshotGenerator()
    {
        $generator = array(
            'hydrator' => '',
            'target' => 'Jobs\Entity\JobSnapshot',
            'exclude' => array('permissions', 'history')
        );
        return $generator;
    }

    /**
     * @param \Jobs\Entity\Classifications $classifications
     *
     * @return self
     */
    public function setClassifications($classifications)
    {
        $this->classifications = $classifications;

        return $this;
    }

    /**
     * @return \Jobs\Entity\Classifications
     */
    public function getClassifications()
    {
        if (!$this->classifications) {
            $this->setClassifications(new Classifications());
        }

        return $this->classifications;
    }

    /**
     * Mark this job as deleted.
     *
     * @internal
     *      This is meant as temporary solution, until
     *      SoftDelete is implemented.
     *
     * @return self
     * @since 0.29
     */
    public function delete()
    {
        $this->isDeleted = true;

        return $this;
    }

    public function isDeleted()
    {
        return $this->isDeleted;
    }
}
