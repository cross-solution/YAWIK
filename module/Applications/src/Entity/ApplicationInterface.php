<?php

namespace Applications\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\SearchableEntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\PermissionsAwareInterface;
use Jobs\Entity\JobInterface;
use Core\Entity\ModificationDateAwareEntityInterface;
use Auth\Entity\InfoInterface;
use Cv\Entity\CvInterface;

interface ApplicationInterface extends
    EntityInterface,
    IdentifiableEntityInterface,
    SearchableEntityInterface,
    ModificationDateAwareEntityInterface,
    PermissionsAwareInterface
{
    const PERMISSION_SUBSEQUENT_ATTACHMENT_UPLOAD = 'subsequentAttachmentUpload';
    
    /**
     * Sets the job this application belongs to.
     *
     * @param JobInterface $job
     * @return ApplicationInterface
     */
    public function setJob(JobInterface $job);
    
    /**
     * Gets the job this application belongs to.
     *
     * @return JobInterface
     */
    public function getJob();
    
    /**
     * Sets the user who created this application.
     *
     * It may be empty (anonymous applicant)
     *
     * @param UserInterface $user
     * @return ApplicationInterface
     */
    public function setUser(UserInterface $user);
    
    /**
     * Gets the user who created this application.
     *
     * May be <b>null</b>.
     *
     * @return UserInterface|null
     */
    public function getUser();
    
    /**
     * Sets the status of this application.
     *
     * If <b>$status</b> is a string, a new {@link \Applications\Entity\Status} instance
     * is created with <b>$status</b> as status name.
     *
     * @param \Applications\Entity\StatusInterface|string $status
     * @return ApplicationInterface
     */
    public function setStatus($status);
    
    /**
     * Gets the status of this application.
     *
     * @return \Applications\Entity\StatusInterface|null
     */
    public function getStatus();
    
    /**
     * Sets the contact info.
     *
     * @param InfoInterface $contact
     * @return ApplicationInterface
     */
    public function setContact(InfoInterface $contact);
    
    /**
     * Gets the contact info
     *
     * @return InfoInterface
     */
    public function getContact();
    
    /**
     * Sets the summary (freetext).
     *
     * @param string $summary
     * @return ApplicationInterface
     */
    public function setSummary($summary);
    
    /**
     * Gets the summary
     *
     * @return string
     */
    public function getSummary();

    /**
     * Sets the facts entity.
     *
     * @param FactsInterface $facts
     *
     * @return self
     */
    public function setFacts(FactsInterface $facts);

    /**
     * Gets the facts.
     *
     * @return FactsInterface
     */
    public function getFacts();

    /**
     * Sets the CV
     *
     * @param CvInterface $cv
     * @return ApplicationInterface
     */
    public function setCv(CvInterface $cv);
    
    /**
     * Gets the CV
     *
     * @return CvInterface
     */
    public function getCv();
    
    
    /**
     * Sets attachments collection.
     *
     * @param Collection $attachments
     * @return ApplicationInterface
     */
    public function setAttachments(Collection $attachments);
    
    /**
     * Gets the collection of attachments.
     *
     * @return Collection
     */
    public function getAttachments();
    
    /**
     * Sets social profiles collection.
     *
     * @param Collection $profiles
     * @return ApplicationInterface
     */
    public function setProfiles(Collection $profiles);
    
    /**
     * Gets social profiles collection.
     *
     * @return Collection
     */
    public function getProfiles();
    
    /**
     * Sets the collection of history entities.
     *
     * @param Collection $history
     * @return ApplicationInterface
     */
    public function setHistory(Collection $history);
    
    /**
     * Gets the collection of history entities.
     *
     * @return Collection
     */
    public function getHistory();
    
    /**
     * Sets the array of user ids of users which has at least once viewed this application.
     *
     * @param array $userIds
     * @return ApplicationInterface
     */
    public function setReadBy(array $userIds);
    
    /**
     * Gets the array of user ids of users which has at least once viewed this application.
     *
     * @return array
     */
    public function getReadBy();
    
    /**
     * Adds a user (id) to the array of user ids of users which has viewed this application.
     *
     * @param UserInterface|string $userOrId
     * @return ApplicationInterface
     */
    public function addReadBy($userOrId);
    
    /**
     * Checks, if a user (id) has not yet read this application.
     *
     * @param UserInterface|string $userOrId
     * @return bool
     */
    public function isUnreadBy($userOrId);
    
    /**
     * Checks, if a user (id) has read this application.
     *
     * @param UserInterface|string $userOrId
     * @return bool
     */
    public function isReadBy($userOrId);
    
    /**
     * Sets the subscriber
     *
     * @param EntityInterface $subscriber
     * @return ApplicationInterface
     */
    public function setSubscriber(EntityInterface $subscriber);
    
    /**
     * Gets the subscriber
     *
     * @return EntityInterface
     */
    public function getSubscriber();
    
    /**
     * Gets all comments for the application.
     *
     * @return ArrayCollection;

     */
    public function getComments();
    
    /**
     * Sets comment collection for the application.
     *
     * @param ArrayCollection $comments
     * @return ApplicationInterface
     */
    public function setComments(ArrayCollection $comments);
    
    /**
     * Gets the internal reference entity
     *
     * @return InternalReferences
     */
    public function getRefs();
    
    /**
     * Gets the average rating of all comments.
     *
     * @param bool $recalculate
     * @return int
     */
    public function getRating($recalculate = false);

    /**
     * Gets all attributes for an application.
     *
     * @return ArrayCollection;

     */
    public function getAttributes();

    /**
     * Sets comment collection for the application.
     *
     * @param Attributes $attributes
     * @return ApplicationInterface
     */
    public function setAttributes(Attributes $attributes);
}
