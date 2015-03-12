<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Entity;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Core\Entity\EntityInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Repository\DoctrineMongoODM\Annotation as Cam;
use Doctrine\Common\Collections\Collection;
use Auth\Entity\UserInterface;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Organizations\Entity\OrganizationInterface;
use Core\Entity\DraftableEntityInterface;
use Core\Entity\Collection\ArrayCollection;

/**
 * The job model
 *
 * @ODM\Document(collection="jobs", repositoryClass="Jobs\Repository\Job")
 */
class Job extends BaseEntity implements JobInterface, DraftableEntityInterface {

    /**
     * unique ID of a job posting used by applications to reference
     * a job
     *
     * @var String
     * @ODM\String @ODM\Index
     **/
    protected $applyId;
    
    /**
     * title of a job posting
     * 
     * @var String 
     * @ODM\String 
     */ 
    protected $title;
    
    /**
     * Description (Free text)
     * 
     * @var String
     * @ODM\String
     */
    protected $description;
    /**
     * name of the publishing company
     * 
     * @var String
     * @ODM\String
     */
    protected $company;
    
    /**
     * publishing company
     *
     * @var OrganizationInterface
     * @ODM\ReferenceOne (targetDocument="\Organizations\Entity\Organization", simple=true, inversedBy="jobs")
     * @ODM\Index
     */
    protected $organization;
    
    
    /**
     * Email Address, which is used to send notifications about e.g. new applications.
     * 
     * @var String
     * @ODM\String
     **/
    protected $contactEmail;
    
    /**
     * the owner of a Job Posting
     *  
     * @var UserInterface $user
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", simple=true)
     * @ODM\Index
     */
    protected $user;
    
    /**
     * all applications of a certain jobad 
     * 
     * @var Collection
     * @ODM\ReferenceMany(targetDocument="Applications\Entity\Application", simple=true, mappedBy="job",
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
     * @ODM\String
     */
    protected $language;
    
    /**
     * location of the job posting. This is a plain text, which describes the location in
     * search e.g. results.
     * 
     * @var String
     * @ODM\String
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
     * @ODM\String
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
     * Status of the job posting
     * 
     * @var String
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
     * @ODM\Boolean
     */
    protected $termsAccepted;
    
    /**
     * Reference of a job opening, on which an applicant can refer to.
     * 
     * @var String
     * @ODM\String 
     */
    protected $reference;
    
    /**
     * Unified Resource Locator to the company-Logo
     *
     * @deprecated (use $organization->image->uri instead)
     * @var String
     * @ODM\String 
     */
    protected $logoRef;

    /**
     * Template-Name
     *
     * @var String
     * @ODM\String
     */
    protected $template;

    /**
     * Application link.
     *
     * @var String
     * @ODM\String
     */
    protected $uriApply;

    /**
     * Unified Resource Locator the Yawik, which handled this job first - so 
     * does know who is the one who has commited this job.
     * 
     * @var String 
     * @ODM\String 
     */
    protected $uriPublisher;
    
    /**
     * this must be enabled to use applications forms etc. for this job or
     * to see number of applications in the list of applications
     * 
     * @var Boolean
     * 
     * @ODM\Boolean 
     */
    protected $atsEnabled;
    
    /**
     * stores a list of lowercase keywords. This array can be regenerated by 
     *   bin/cam jobs generatekeywords
     * 
     * @ODM\Collection
     */
    protected $keywords;
    
    
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
     * @ODM\Hash*/
    protected $portals = array();

    /**
     * Flag indicating draft state of this job.
     *
     * @var bool
     * @ODM\Boolean
     */
    protected $isDraft = false;

    /**
     * Is this needed?
     * 
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
    public function setApplyId($applyId) {
        $this->applyId = (string) $applyId;
        return $this;
    }
    /**
     * @see \Jobs\Entity\JobInterface::getApplyId()
     * @return String
     */
    public function getApplyId() {
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
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title of a job posting
     *
     * @see \Jobs\Entity\JobInterface::setTitle()
     * @param String $title
     * @return \Jobs\Entity\JobInterface
     */
    public function setTitle($title) {
        $this->title = (string) $title;
        return $this;
    }

    /**
     * Sets the description of a job position
     *
     * @see \Jobs\Entity\JobInterface::setDescription()
     * @param String $text
     * @return \Jobs\Entity\JobInterface
     */
    public function setDescription($text)
    {
        $this->description = (string) $text;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getDescription()
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getCompany()
     */
    public function getCompany() {
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
    public function getOrganization() {
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
            $email = False;
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
        return $this->locations;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setUser()
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
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getUser()
     */
    public function getUser() {
        return $this->user;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setApplications()
     */
    public function setApplications(Collection $applications) {
        $this->applications = $applications;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getApplications()
     */
    public function getApplications() {
        return $this->applications;
    }
    /**
     * Gets the number of unread applications
     * @return Collection
     */
    public function getUnreadApplications() {
        return $this->unreadApplications;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getLink()
     */
    public function getLink() {
        return $this->link;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setLink()
     */
    public function setLink($link) {
        $this->link = $link;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::getDatePublishStart()
     */
    public function getDatePublishStart() {
        return $this->datePublishStart;
    }
    /**
     * (non-PHPdoc)
     * @param string $datePublishStart
     * @see \Jobs\Entity\JobInterface::setDatePublishStart()
     * @return $this
     */
    public function setDatePublishStart($datePublishStart) {
        $this->datePublishStart = $datePublishStart;
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
    public function getStatus() {
        return $this->status;
    }
    /**
     * (non-PHPdoc)
     * @see \Jobs\Entity\JobInterface::setStatus()
     */
    public function setStatus($status) {
        if (!$status instanceOf Status) {
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
        if (Null == $this->history) {
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
    public function getReference() {
        return $this->reference;
    }
    /**
     * (non-PHPdoc)
     * @see JobInterface::setReference()
     */
    public function setReference($reference) {
        $this->reference = $reference;
        return $this;
    }
    /**
     * checks, weather a job is enabled for getting applications
     * @return boolean
     */
    public function getAtsEnabled() {
        return $this->atsEnabled;
    }
    /**
     * enables a job add to receive applications
     * 
     * @param boolean $atsEnabled
     * @return \Jobs\Entity\Job
     */
    public function setAtsEnabled($atsEnabled) {
        $this->atsEnabled = $atsEnabled;
        return $this;
    }
    /**
     * returns an uri to the organisations logo
     *
     * @deprecated
     * @return string
     */
    public function getLogoRef() {
        /** @var $organization \Organizations\Entity\Organization */
        $organization = $this->organization;
        if (isset($organization) && isset($organization->image)) {
            $organizationImage = $organization->image;
            return "/file/Organizations.OrganizationImage/" . $organizationImage->id;
        }
        return $this->logoRef;
    }
    /**
     * Set the uri to the organisations logo
     *
     * @deprecated
     * @param string $logoRef
     * @return \Jobs\Entity\Job
     */
    public function setLogoRef($logoRef) {
        $this->logoRef = $logoRef;
        return $this;
    }

    /**
     *
     *
     * @return string
     */
    public function getTemplate() {
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
    public function setTemplate($template) {
        $this->template = $template;
        return $this;
    }



    /**
     * Gets the uri of an application link
     *
     * @return String
     */
    public function getUriApply() {
        return $this->uriApply;
    }

    /**
     * Sets the uri of an application link
     *
     * @param String $uriApply
     * @return \Jobs\Entity\Job
     */
    public function setUriApply($uriApply) {
        $this->uriApply = $uriApply;
        return $this;
    }

    /**
     * Gets the uri of a publisher
     *
     * @return String
     */
    public function getUriPublisher() {
        return $this->uriPublisher;
    }
    /**
     * 
     * @param String $uriPublisher
     * @return \Jobs\Entity\Job
     */
    public function setUriPublisher($uriPublisher) {
        $this->uriPublisher = $uriPublisher;
        return $this;
    }
    
    /**
     * Get a list of fieldnames, which can be searched by keywords
     *
     * @return array
     */
    public function getSearchableProperties()
    {
        return array('title', 'company', 'location', 'applyId', 'reference');
    }
    
    /**
     * (non-PHPdoc)
     * @see \Core\Entity\SearchableEntityInterface::setKeywords()
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Core\Entity\SearchableEntityInterface::getKeywords()
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Core\Entity\SearchableEntityInterface::clearKeywords()
     */
    public function clearKeywords()
    {
        $this->keywords = array();
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
        if (!$this->templateValues instanceOf TemplateValues) {
            $this->templateValues = new TemplateValues();
        }
        return $this->templateValues;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateValues(EntityInterface $templateValues = null)
    {
        if (!$templateValues instanceOf TemplateValues) {
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
     * @return Array
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
        return !$this->isDraft && $this->status->name == 'active';
    }
}