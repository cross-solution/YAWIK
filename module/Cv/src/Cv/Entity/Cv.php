<?php

namespace Cv\Entity;

use Auth\Entity\InfoInterface;
use Auth\Entity\UserInterface;
use Core\Collection\IdentityWrapper;
use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\DraftableEntityInterface;
use Core\Entity\ModificationDateAwareEntityTrait;
use Core\Entity\PermissionsAwareTrait;
use Core\Entity\PermissionsInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Defines CV Model
 *
 * @ODM\Document(collection="cvs", repositoryClass="\Cv\Repository\Cv")
 * @ODM\Indexes({
 *     @ODM\Index(keys={
 *          "preferredJob.desiredJob"="text"
 *     },name="cvFulltext")
 * })
 * @ODM\HasLifecycleCallbacks
 */
class Cv extends AbstractIdentifiableEntity implements CvInterface, ResourceInterface
{
    use PermissionsAwareTrait, ModificationDateAwareEntityTrait;

    /**
     * Owner of the CV
     *
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="\Auth\Entity\User", storeAs="id", cascade="persist")
     */
    protected $user;
    
    /**
     * personal informations, contains firstname, lastname, email,
     * phone etc.
     *
     * @ODM\EmbedOne(targetDocument="Contact")
     */
    protected $contact;
    
    /**
     * Education History
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Education")
     */
    protected $educations;
    
    /**
     * Employment History
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Employment")
     */
    protected $employments;
    
    /**
     * Skills
     *
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Skill")
     */
    protected $skills;

    /**
    * Skills
    *
    * @var ArrayCollection
    * @ODM\EmbedMany(targetDocument="\Cv\Entity\Language")
    */
    protected $languageSkills;

    /**
     * @var array
     * @ODM\Field(type="collection")
     */
    protected $nativeLanguages=[];


    /**
     * Preferred Job. Where do the user want to work? What kind of work he wants do do
     *
     * @ODM\EmbedOne(targetDocument="\Cv\Entity\PreferredJob")
     */
    protected $preferredJob;

    /**
     * Flag indicating draft state of this cv.
     *
     * @var bool
     * @ODM\Field(type="boolean")
     */
    protected $isDraft = false;
    
    /**
     * Status
     *
     * @var Status
     * @ODM\EmbedOne(targetDocument="Status")
     * @ODM\Index
     */
    protected $status;
    
    /**
     * Multiple attachments
     *
     * @since 0.26
     * @ODM\ReferenceMany(targetDocument="Attachment", storeAs="id", cascade={"persist", "remove"})
     */
    protected $attachments;

    public function __construct()
    {
        $this->status = new Status();
    }
    
    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $oldUser    = $this->user;
        $this->user = $user;
        $this->updatePermissions($oldUser);

        return $this;
    }

    /**
     * Returns the string identifier of the Resource
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Entity/Cv';
    }


    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
    
    /**
     * @return Cv
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
     * @return ArrayCollection
     */
    public function getEducations()
    {
        if (!$this->educations) {
            $this->setEducations(new ArrayCollection());
        }
        return $this->educations;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getEducationsIndexedById()
    {
        return new IdentityWrapper($this->getEducations());
    }

    /**
     * @param CollectionInterface $educations
     * @return $this
     */
    public function setEducations(CollectionInterface $educations)
    {
        $this->educations = $educations;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEmployments()
    {
        if (!$this->employments) {
            $this->setEmployments(new ArrayCollection());
        }
        return $this->employments;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getEmploymentsIndexedById()
    {
        return new IdentityWrapper($this->getEmployments());
    }

    /**
     * @param CollectionInterface $employments
     * @return $this
     */
    public function setEmployments(CollectionInterface $employments)
    {
        $this->employments = $employments;
        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getSkills()
    {
        if (!$this->skills) {
            $this->setSkills(new ArrayCollection());
        }
        return $this->skills;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getSkillsIndexedById()
    {
        return new IdentityWrapper($this->getSkills());
    }

    /**
     * @param CollectionInterface $skills
     * @return $this
     */
    public function setSkills(CollectionInterface $skills)
    {
        $this->skills = $skills;
        return $this;
    }

    /**
     * @param bool $isDraft
     * @return $this
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft=$isDraft;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * @return \Cv\Entity\PreferredJobInterface
     */
    public function getPreferredJob()
    {
        if (null == $this->preferredJob) {
            $this->preferredJob = new PreferredJob();
        }
        return $this->preferredJob;
    }

    /**
     * @param \Cv\Entity\PreferredJobInterface $preferredJob
     * @return $this
     */
    public function setPreferredJob(PreferredJobInterface $preferredJob)
    {
        $this->preferredJob = $preferredJob;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLanguageSkills()
    {
        if (!$this->languageSkills) {
            $this->setLanguageSkills(new ArrayCollection());
        }
        return $this->languageSkills;
    }

    /**
     * @param CollectionInterface $languageSkills
     * @return $this
     */
    public function setLanguageSkills(CollectionInterface $languageSkills)
    {
        $this->languageSkills = $languageSkills;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getLanguageSkillsIndexedById()
    {
        return new IdentityWrapper($this->getLanguageSkills());
    }

    /**
     * Sets the mothers tongue of the candidate
     *
     * @param array
     * @return $this
     */
    public function setNativeLanguages($nativeLanguages)
    {
        $this->nativeLanguages=$nativeLanguages;
        return $this;
    }

    /**
     * Gets the mothers tongue of the candidate
     *
     * @return string
     */
    public function getNativeLanguages()
    {
        return $this->nativeLanguages;
    }
    
    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * @param Status|string $status
     */
    public function setStatus($status)
    {
        if (!$status instanceof Status) {
            $status = new Status($status);
        }
    
        $this->status = $status;

        /* Update file permissions */
        $perms = $this->getPermissions();
        if ($status == StatusInterface::PUBLIC_TO_ALL) {
            $perms->grant('all', PermissionsInterface::PERMISSION_VIEW);
        } else {
            $perms->revoke('all', PermissionsInterface::PERMISSION_VIEW);
        }

        return $this;
    }

    /**
     * @param CollectionInterface $attachments
     * @return Cv
     * @since 0.26
     */
    public function setAttachments(CollectionInterface $attachments)
    {
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * @return CollectionInterface
     * @since 0.26
     */
    public function getAttachments()
    {
        if (!$this->attachments) {
            $this->setAttachments(new ArrayCollection());
        }
        return $this->attachments;
    }

    /**
     *
     * @param PermissionsInterface $permissions
     */
    private function setupPermissions(PermissionsInterface $permissions = null)
    {
        if ($this->user) {
            $permissions->grant($this->user, PermissionsInterface::PERMISSION_ALL);
        }
    }

    private function updatePermissions($oldUser = null)
    {
        $hasPermissions = (bool) $this->permissions;
        $permissions = $this->getPermissions();

        if ($hasPermissions) {
            $oldUser && $permissions->revoke($oldUser, PermissionsInterface::PERMISSION_ALL);
            $this->setupPermissions($permissions);
        }

        /*
         * getPermissions() already granted the user we need not to do anything.
         */
    }
}
