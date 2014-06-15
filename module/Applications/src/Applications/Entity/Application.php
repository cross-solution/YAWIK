<?php
/**
 * @package Applications
 */
namespace Applications\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\EntityInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Auth\Entity\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Jobs\Entity\JobInterface;
use Doctrine\Common\Collections\Collection;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Core\Entity\PreUpdateAwareInterface;
use Core\Entity\AbstractIdentifiableModificationDateAwareEntity;
use Auth\Entity\InfoInterface;
use Cv\Entity\CvInterface;

/**
 * The application model
 * 
 * @author mathias
 *
 * @ODM\Document(collection="applications", repositoryClass="Applications\Repository\Application") @ODM\HasLifecycleCallbacks
 */
class Application extends AbstractIdentifiableModificationDateAwareEntity 
                  implements ApplicationInterface, 
                             ResourceInterface
{
   
    /**
     * Refering job
     * 
     * @var JobInterface
     * @ODM\ReferenceOne(targetDocument="Jobs\Entity\Job", simple=true, inversedBy="applications")
     */
    protected $job;
    
    /**
     * User, who owns the application
     *
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="Auth\Entity\User", simple=true)
     */
    protected $user;
    
    /**
     * Status of an application.
     * 
     * @var StatusInterface
     * @ODM\EmbedOne(targetDocument="Status")
     */
    protected $status;
    
    /**
     * personal informations, contains firstname, lastname, email, 
     * phone etc.
     *
     * @ODM\EmbedOne(targetDocument="Contact")
     */
    protected $contact;
    
    /**
     * The summary of an application
     * 
     * @var String
     * @ODM\String
     */
    protected $summary;
    
    /**
     * Resume, containing employments, educations and skills
     *
     * @var CvInterface
     * @ODM\EmbedOne(targetDocument="Cv")
     */
    protected $cv;

    /**
     * multiple Attachments of an application
     * 
     * @ODM\ReferenceMany(targetDocument="Attachment", simple="true", cascade={"persist", "remove"})
     */
    protected $attachments;
    
    /**
     * Searchable keywords.
     * 
     * @var array
     * @ODM\Collection
     */
    protected $keywords;
    
    /**
     * History on an application
     * 
     * @var Collection
     * @ODM\EmbedMany(targetDocument="History")
     */
    protected $history;
        
    /**
     * Flag, wether privacy policy is accepted or not.
     * 
     * @var bool
     */
    protected $privacyPolicy;
    
    /**
     * User ids of users which has read this application.
     * 
     * @var array
     * @ODM\Collection
     */
    protected $readBy = array();
     
    /**
     * Refering subscriber (Where did the application origin from).
     * 
     * @ODM\ReferenceOne(targetDocument="Subscriber", simple=true)
     */
    protected $subscriber;
    
    
    /**
     * Comments
     * 
     * @var Collection
     * @ODM\EmbedMany(targetDocument="Comment")
     */
    protected $comments;
    
    /**
     * Average rating from all comments.
     * 
     * @var int
     * @ODM\Int
     */
    protected $rating;
    
    /**
     * Assigned permissions.
     * 
     * @var PermissionsInterface
     * @ODM\EmbedOne(targetDocument="\Core\Entity\Permissions")
     */
    protected $permissions;
    
    /**
     * Internal references (DB denaturalism)
     *  
     * @var InternalReferences 
     * @ODM\EmbedOne(targetDocument="InternalReferences")
     */
    protected $refs;
    
    /**
     * Collection of social network profiles.
     * 
     * @var Collection
     * @see \Auth\Entity\SocialProfiles\ProfileInterface
     * @ODM\EmbedMany(discriminatorField="_entity")
     */
    protected $profiles;
    
    /** @ODM\PreUpdate */
    public function preUpdate()
    {
        $this->recalculateRatings(false);
        return $this;
    }
    
    /** @ODM\PrePersist */
    public function prePersist()
    {  
        $this->recalculateRatings(true);
        $this->setDateCreated(new \DateTime());
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function recalculateRatings($isNew = false)
    {
        parent::preUpdate($isNew);
        
        // Compute rating value.
        // @todo Need to know wether comments has changed or not.
        // Unfortunately, persistent collection gets no dirty flag,
        // if existing entries are changed....
        // We limit recalculates to the cases where comments gets loaded from
        // the database (which still does not neccessarly mean, there are changes...
        
        $comments = $this->getComments();
        if ($isNew 
            || $comments instanceOf ArrayCollection // new Comments
            || $comments->isInitialized() // Comments were loaded and eventually changed (we do not know)
            || $comments->isDirty() // new Comments added w/o initializing
        ) {
            $this->getRating(/*recalculate*/ true);
        }

    }
    
    /**
     * {@inheritDoc}
     * @see \Zend\Permissions\Acl\Resource\ResourceInterface::getResourceId()
     */
    public function getResourceId()
    {
        return 'Entity/Application';
    }
    
    
    
    /**
     * {@inheritDoc}
     * 
     * @see \Applications\Entity\ApplicationInterface::getJob()
     */
    public function getJob()
    {
        return $this->job;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return \Applications\Entity\Application
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;
        
        $this->getRefs()->setJob($job);
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return Application
     * @see \Applications\Entity\ApplicationInterface::setUser()
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
     * {@inheritDoc}
     * 
     * @see \Applications\Entity\ApplicationInterface::getUser()
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritDoc
     * @return Application
     * @see \Applications\Entity\ApplicationInterface::setStatus()
     */
    public function setStatus($status)
    {
        if (!$status instanceOf Status) {
            $status = new Status($status);
        } 
        $this->status = $status;
        return $this;
    }
    
    /**
     * Modifies the state of an application.
     * 
     * Creates a history entry.
     * 
     * @param StatusInterface|string $status
     * @param string $message
     * @return Application
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
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getStatus()
     */
    public function getStatus()
    {
        return $this->status;
    }
    
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::getContact()
	 */
    public function getContact ()
    {
        return $this->contact;
    }

	/**
	 * {@inheritDoc}
	 * @see ApplicationInterface::setContact()
	 * @return Application
     */
    public function setContact (InfoInterface $contact)
    {
        $this->contact = $contact;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::setSummary()
     * @return Application
     */
    public function setSummary($summary)
    {
        $this->summary = (string) $summary;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getSummary()
     * @return Application
     */
    public function getSummary()
    {
        return $this->summary;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::setCv()
     * @return Application
     */
	public function setCv(CvInterface $cv)
	{
	    $this->cv = $cv;
	    return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::getCv()
	 */
	public function getCv()
	{
	    if (is_null($this->cv)){
	        $this->cv= new Cv();
	    }
	    return $this->cv;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::setAttachments()
	 * @return Application
	 */
	public function setAttachments(Collection $attachments)
	{
	    $this->attachments = $attachments;
	    return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::getAttachments()
	 */
	public function getAttachments()
	{
	    if (!$this->attachments) {
	        $this->setAttachments(new ArrayCollection());
	    }
	    return $this->attachments;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::setProfiles()
	 * @return Application
	 */
	public function setProfiles(Collection $profiles)
	{
	    $this->profiles = $profiles;
	    return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::getProfiles()
	 */
	public function getProfiles()
	{
	    if (!$this->profiles) {
	        $this->setProfiles(new ArrayCollection());
	    }
	    return $this->profiles;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::setHistory()
	 * @return Application
	 */
	public function setHistory(Collection $history)
	{
	    $this->history = $history;
	    return $this;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::getHistory()
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
	 * @see \Applications\Entity\ApplicationInterface::setPrivacyPolicyAccepted()
	 * @return Application
	 */
	public function setPrivacyPolicyAccepted($privacyPolicy)
	{
	    $this->privacyPolicy = $privacyPolicy;
	    return $this;
	}
        
	/**
	 * {qinheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::getPrivacyPolicyAccepted()
	 */
	public function getPrivacyPolicyAccepted()
	{
	    return $this->privacyPolicy;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Applications\Entity\ApplicationInterface::setReadBy()
	 * @return Application
	 */
    public function setReadBy(array $userIds)
    {
        $this->readBy = $userIds;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getReadBy()
     */
    public function getReadBy()
    {
        return $this->readBy;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::addReadBy()
     * @return Application
     */
    public function addReadBy($userOrId)
    {
        if ($userOrId instanceOf UserInterface) {
            $userOrId = $userOrId->getId();
        }
        if (!in_array($userOrId, $this->readBy)) {
            $this->readBy[] = $userOrId;
        }
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::isUnreadBy()
     */
    public function isUnreadBy($userOrId) 
    {
        return !$this->isReadBy($userOrId);
    }
     
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::isReadBy()
     */
    public function isReadBy($userOrId)
    {
        if ($userOrId instanceOf UserInterface) {
            $userOrId = $userOrId->getId();
        }
        
        return in_array($userOrId, $this->readBy);
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getSubscriber()
     */
    public function getSubscriber() {
        return $this->subscriber;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::setSubscriber()
     * @return Application
     */
    public function setSubscriber(EntityInterface $subscriber) {
        $this->subscriber = $subscriber;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsAwareInterface::getPermissions()
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
     * {@inheritDoc}
     * @see \Core\Entity\PermissionsAwareInterface::setPermissions()
     * @return Application
     */
    public function setPermissions(PermissionsInterface $permissions) {
        $this->permissions = $permissions;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getRefs()
     */
    public function getRefs()
    {
        if (!$this->refs) {
            $this->refs = new InternalReferences();
        }
        return $this->refs;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Entity\SearchableEntityInterface::getSearchableProperties()
     */
    public function getSearchableProperties()
    {
        return array('summary');
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Entity\SearchableEntityInterface::setKeywords()
     * @return Application
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Entity\SearchableEntityInterface::getKeywords()
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Entity\SearchableEntityInterface::clearKeywords()
     * @return Application
     */
    public function clearKeywords()
    {
        $this->keywords = array();
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getComments()
     */
    public function getComments()
    {
        if (!$this->comments) {
            $this->setComments(new ArrayCollection());
        }
        return $this->comments;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::setComments()
     * @return Application
     */
    public function setComments(Collection $comments)
    {
        $this->comments = $comments;
        return $this;
        
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getRating()
     */
    public function getRating($recalculate = false)
    {
        if ($recalculate || null === $this->rating) {
            $sum = 0;
            $count = 0;
            foreach ($this->getComments() as $comment) {
                $sum += $comment->getRating()->getAverage();
                $count += 1;
            }
            $this->rating = 0 == $count ? 0 : round($sum / $count);
        }
        return $this->rating;
    }
    
}