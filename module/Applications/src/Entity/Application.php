<?php
/**
 * @package Applications
 */
namespace Applications\Entity;

use Core\Entity\EntityInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Auth\Entity\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Jobs\Entity\JobInterface;
use Doctrine\Common\Collections\Collection;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Core\Entity\AbstractIdentifiableModificationDateAwareEntity;
use Auth\Entity\InfoInterface;
use Cv\Entity\CvInterface;
use Core\Entity\DraftableEntityInterface;
use Auth\Entity\AnonymousUser;

/**
 * The application. This document holds the complete application. It references all attached data like
 * attachments, ratings, status changes. etc.
 *
 * @author mathias
 *
 * @ODM\Document(collection="applications", repositoryClass="Applications\Repository\Application")
 * @ODM\HasLifecycleCallbacks
 */
class Application extends AbstractIdentifiableModificationDateAwareEntity implements
    ApplicationInterface,
    ResourceInterface,
    DraftableEntityInterface
{
   
    /**
     * Refering job
     *
     * @var JobInterface
     * @ODM\ReferenceOne(targetDocument="Jobs\Entity\Job", storeAs="id", inversedBy="applications")
     * @ODM\Index
     */
    protected $job;
    
    /**
     * User, who owns the application
     *
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="Auth\Entity\User", storeAs="id")
     * @ODM\Index
     */
    protected $user;
    
    protected $__anonymousUser__;
    
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
     * The cover letter of an application
     *
     * @var String
     * @ODM\Field(type="string")
     */
    protected $summary;

    /**
     * The facts of this application.
     *
     * @ODM\EmbedOne(targetDocument="\Applications\Entity\Facts")
     * @var FactsInterface
     */
    protected $facts;

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
     * @ODM\ReferenceMany(targetDocument="Attachment", storeAs="id", cascade={"persist", "remove"})
     */
    protected $attachments;
    
    /**
     * Searchable keywords.
     *
     * @var array
     * @ODM\Field(type="collection")
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
     * Who has opened the detail view of the application. Contains an array of user ids, which has read this
     * application.
     *
     * @var array
     * @ODM\Field(type="collection")
     */
    protected $readBy = array();
     
    /**
     * Refering subscriber (Where did the application origin from).
     *
     * @ODM\ReferenceOne(targetDocument="Subscriber", cascade={"persist"}, storeAs="id")
     */
    protected $subscriber;
    
    
    /**
     * Recruiters can comment an application.
     *
     * @var Collection
     * @ODM\EmbedMany(targetDocument="Comment")
     */
    protected $comments;
    
    /**
     * Average rating from all comments.
     *
     * @var int
     * @ODM\Field(type="int")
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
    
    
    /**
     * Flag indicating draft state of this application.
     *
     * @var bool
     * @ODM\Field(type="boolean")
     */
    protected $isDraft = false;
    
    /**
     * Attributes like "privacy policy accepted" or "send by data as an CC".
     *
     * @var \Applications\Entity\Attributes
     * @ODM\EmbedOne(targetDocument="Attributes")
     */
    protected $attributes;
    
    /**
     * {@inheritDoc}
     * @ODM\PreUpdate
     * @ODM\PrePersist
     */
    public function recalculateRatings()
    {
        // Compute rating value.
        // @todo Need to know weather comments has changed or not.
        // Unfortunately, persistent collection gets no dirty flag,
        // if existing entries are changed....
        // We limit recalculates to the cases where comments gets loaded from
        // the database (which still does not neccessarly mean, there are changes...

        $comments = $this->getComments();
        if ($comments instanceof ArrayCollection // new Comments
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
     * @see \Core\Entity\DraftableEntityInterface::isDraft()
     */
    public function isDraft()
    {
        return $this->isDraft;
    }
    
    /**
     * {@inheritDoc}
     * @return \Applications\Entity\Application
     */
    public function setIsDraft($flag)
    {
        $this->isDraft = (bool) $flag;
        return $this;
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
        $this->getPermissions()->grant($user, Permissions::PERMISSION_ALL);
        $this->user = $user;
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
     *
     * @ODM\PrePersist
     * @ODM\PreUpdate
     * @ODM\PreFlush
     */
    public function prependPersistingAnonymousUser()
    {
        if ($this->user instanceof AnonymousUser) {
            $this->__anonymousUser__ = $this->user;
            $this->user = null;
        }
    }
    
    /**
     *
     * @ODM\PostPersist
     * @ODM\PostUpdate
     */
    public function restoreAnonymousUser()
    {
        if ($this->__anonymousUser__) {
            $this->user = $this->__anonymousUser__;
            $this->__anonymousUser__ = null;
        }
    }

    /**
     * {@inheritDoc
     * @return Application
     * @see \Applications\Entity\ApplicationInterface::setStatus()
     */
    public function setStatus($status)
    {
        if (!$status instanceof Status) {
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
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * {@inheritDoc}
     * @see ApplicationInterface::setContact()
     * @return Application
     */
    public function setContact(InfoInterface $contact)
    {
        if (!$contact instanceof Contact) {
            $contact = new Contact($contact);
        }
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

    public function setFacts(FactsInterface $facts)
    {
        $this->facts = $facts;

        return $this;
    }

    public function getFacts()
    {
        if (!$this->facts) {
            $this->setFacts(new Facts());
        }

        return $this->facts;
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
        if (is_null($this->cv)) {
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
        if (null == $this->history) {
            $this->setHistory(new ArrayCollection());
        }
        return $this->history;
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
        if ($userOrId instanceof UserInterface) {
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
        if ($userOrId instanceof UserInterface) {
            $userOrId = $userOrId->getId();
        }
        
        return in_array($userOrId, $this->readBy);
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::getSubscriber()
     * @return \Applications\Entity\SubscriberInterface
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }
    
    /**
     * {@inheritDoc}
     * @see \Applications\Entity\ApplicationInterface::setSubscriber()
     * @return Application
     */
    public function setSubscriber(EntityInterface $subscriber)
    {
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
            $permissions = new Permissions('Application');
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
    public function setPermissions(PermissionsInterface $permissions)
    {
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
     * @deprecated
     * @see \Core\Entity\SearchableEntityInterface::getSearchableProperties()
     */
    public function getSearchableProperties()
    {
        return array('summary', 'commentsMessage');
    }
    
    /**
     * {@inheritDoc}
     * @deprecated
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
     * @deprecated
     * @see \Core\Entity\SearchableEntityInterface::getKeywords()
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
    
    /**
     * {@inheritDoc}
     * @deprecated
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
    public function setComments(ArrayCollection $comments)
    {
        $this->comments = $comments;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getCommentsMessage()
    {
        $comments = array();
        if ($this->comments) {
            foreach ($this->getComments() as $comment) {
                $comments[] = $comment->getMessage();
            }
        }
        return $comments;
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
    
    public function setAttributes(Attributes $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
    
    public function getAttributes()
    {
        if (!$this->attributes) {
            $this->setAttributes(new Attributes());
        }
        return $this->attributes;
    }
}
