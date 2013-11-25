<?php

namespace Applications\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\EntityInterface;
use Core\Entity\CollectionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @todo write interface
 * @author mathias
 *
 */
class Application extends AbstractIdentifiableEntity implements ApplicationInterface, ResourceInterface
{
    protected $jobId;
    protected $job;
    
    /*
     * new
     */
    protected $status;
    protected $dateCreated;
    protected $dateModified;

    /*
     * personal informations, contains firstname, lastname, email, 
     * phone etc.
     */
    protected $contact;
    
    protected $summary;
    
    /*
     * Resume, containing employments, educations and skills
     */
    protected $cv;

    protected $attachments;
    
    protected $history;
    
    /*
     * Administrative 
     */
    
    protected $privacyPolicy;
    
    public function getResourceId()
    {
        return 'Entity/Application';
    }
    
    /**
     * @return the $jobId
     */
    public function getJobId ()
    {
        if (!$this->jobId && ($job = $this->getJob())) {
            $this->setJobId($job->getId());
        }
        return $this->jobId;
    }

	/**
     * @param field_type $jobId
     */
    public function setJobId ($jobId)
    {
        $this->jobId = $jobId;
    }
    
    public function getJob()
    {
        return $this->job;
    }
    
    public function injectJob(EntityInterface $job)
    {
        $this->job = $job;
        $this->setJobId($job->getId());
        return $this;
    }

    public function setStatus($status)
    {
        if (!$status instanceOf Status) {
            $status = new Status($status);
        } 
        $this->status = $status;
        return $this;
    }
    
    public function changeStatus($status)
    {
        $this->setStatus($status);
        $status = $this->getStatus(); // ensure StatusEntity
        
        $this->getHistory()->addFromStatus($status);
        return $this;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getDateCreated ($format=null)
    {
        if (!$this->dateCreated) {
            $this->setDateCreated('now');
        }
        return null !== $format
            ? strftime($format, $this->dateCreated->getTimestamp())
            : $this->dateCreated;
    }
    
    public function setDateCreated ($dateCreated)
    {
        if (is_string($dateCreated)) {
            $dateCreated = new \DateTime($dateCreated);
        }
        
        if (!$dateCreated instanceOf \DateTime) {
            $dateCreated = new \DateTime();
        }
        
        $this->dateCreated = $dateCreated;
    }
    
    public function getDateModified ($format=null)
    {
        if (!$this->dateModified) {
            $this->setDateModified('now');
        }
        return null !== $format
            ? $this->dateModified->format($format)
            : $this->dateModified;
    }
    
    public function setDateModified ($dateModified)
    {
        if (is_string($dateModified)) {
            $dateCreated = new \DateTime($dateModified);
        }
    
        if (!$dateModified instanceOf \DateTime) {
            $dateModified = new \DateTime();
        }
    
        $this->dateModified = $dateModified;
    }
    
	/**
     * @return the $contact
     */
    public function getContact ()
    {
        return $this->contact;
    }

	/**
     * @param field_type $contact
     */
    public function setContact (EntityInterface $contact)
    {
        $this->contact = $contact;
        return $this;
    }

    public function setSummary($summary)
    {
        $this->summary = (string) $summary;
        return $this;
    }
    
    public function getSummary()
    {
        return $this->summary;
    }
    
	public function setCv(EntityInterface $cv)
	{
	    $this->cv = $cv;
	    return $this;
	}
	
	public function getCv()
	{
	    return $this->cv;
	}
	
	public function injectAttachments(CollectionInterface $attachments)
	{
	    $this->attachments = $attachments;
	    return $this;
	}
	
	public function getAttachments()
	{
	    return $this->attachments;
	}
	
	public function setHistory(HistoryCollectionInterface $history)
	{
	    $this->history = $history;
	    return $this;
	}
	
	public function getHistory()
	{
	    return $this->history;
	}
        
	public function setPrivacyPolicyAccepted($privacyPolicy)
	{
	    $this->privacyPolicy = $privacyPolicy;
	    return $this;
	}
        
	public function getPrivacyPolicyAccepted()
	{
	    return $this->privacyPolicy;
	}
}