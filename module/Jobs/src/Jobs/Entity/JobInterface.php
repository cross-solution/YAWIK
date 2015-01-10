<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Entity;

use Organizations\Entity\OrganizationInterface;
use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;
use Core\Entity\SearchableEntityInterface;
use Core\Entity\ModificationDateAwareEntityInterface;
use Core\Entity\PermissionsAwareInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Interface for a Job Posting
 *
 * @package Jobs\Entity
 */
interface JobInterface extends EntityInterface,
                               IdentifiableEntityInterface,
                               ModificationDateAwareEntityInterface, 
                               SearchableEntityInterface,
                               PermissionsAwareInterface,
                               ResourceInterface
{

    /**
     * Gets the unique key used by applications to reference a job posting
     *
     * @param string $applyId
     */
    public function setApplyId($applyId);

    /**
     * Sets a unique key used by applications to reference a job posting
     *
     * @return string
     */
    public function getApplyId();
    
    /**
     * Gets an URI for a job posting
     *
     * @return string
     */    
    public function getLink();

    /**
     * Sets an URI for a job posting
     *
     * @param string $link
     */
    public function setLink($link);
    
    /**
     * Gets the publishing date of a job posting
     *
     * @return string
     */
    public function getDatePublishStart();
    
    /**
     * Sets the publishing date of a job posting
     *
     * @param $datePublishStart
     * @return string
     */
    public function setDatePublishStart($datePublishStart);
    
    /**
     * Gets the title of a job posting
     *
     * @return string $title
     */
    
    public function getTitle();

    /**
     * Sets the title of a job posting
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Gets the description of a job posting
     *
     * @return string
     */
    public function getDescription();
    /**
     * Sets the description of a job posting
     *
     * @param string $text
     */
    public function setDescription($text);

    /**
     * Gets the organisation name, which offers the job posting
     *
     * @deprecated
     * @return string
     */
    public function getCompany();

    /**
     * Sets the organisation name, which offers a job posting
     *
     * @deprecated
     * @param string $company
     * @return JobInterface $job
     */
    public function setCompany($company);

    /**
     * Gets the organisation, which offers the job posting
     * 
     * @return OrganizationInterface
     */

    public function getOrganization();
    
    /**
     * Sets the organization, which offers the job
     * 
     * @param OrganizationInterface $organization
     * @return JobInterface
     */
    public function setOrganization(OrganizationInterface $organization = null);

    /**
     * Sets the contact email of a job posting
     *
     * @param string $email
     * @return JobInterface $job
     */
    public function setContactEmail($email);
    
    /**
     * Gets the contact email a job posting
     *
     * @return string
     */
    public function getContactEmail();

    /**
     * Sets the user, who owns a job posting
     *
     * @param UserInterface $user
     * @return JobInterface $job
     */
    public function setUser(UserInterface $user);

    /**
     * Gets the user, who owns a job posting
     *
     * @return UserInterface $user
     */
    public function getUser() ;

    /**
     * Gets the link to the application form
     *
     * @return String
     */
    public function getUriApply();

    /**
     * Sets the Link to the application form
     *
     * @param String $uriApply
     * @return \Jobs\Entity\Job
     */
    public function setUriApply($uriApply);


    /**
     * Gets the URI of the publisher
     *
     * @return String
     */
    public function getUriPublisher();

    /**
     * Sets the URI of the publisher
     *
     * @param String $uriPublisher
     * @return \Jobs\Entity\Job
     */
    public function setUriPublisher($uriPublisher);

    /**
     * Sets the language of a job posting
     *
     * @param string $language
     */
    public function setLanguage($language);

    /**
     * Gets the language of a job posting
     *
     * @return string
     */
    public function getLanguage();


    /**
     * Sets the location of a job posting
     *
     * @param string $location
     */
    public function setLocation($location);

    /**
     * Gets the location of a job posting
     *
     * @return string
     */
    public function getLocation();

    /**
     * Sets locations of a job posting
     *
     * @param string $locations
     */
    public function setLocations($locations);

    /**
     * Gets locations of a job posting
     *
     * @return string
     */
    public function getLocations();

    /**
     * Sets applications for a job posting
     * 
     * @param Collection $applications
     */
    public function setApplications(Collection $applications);
    
    /**
     * Gets applications for a job posting
     *
     * @return Collection $applications
     */
    public function getApplications();
    
    /**
     * Sets Status of a job posting
     *
     * @param string $status
     */
    public function setStatus($status);

    /**
     * Gets applications for a job posting
     *
     * @return string
     */
    public function getStatus();

    /**
     * Sets the collection of history entities.
     *
     * @param Collection $history
     * @return JobInterface
     */
    public function setHistory(Collection $history);

    /**
     * Gets the collection of history entities.
     *
     * @return Collection
     */
    public function getHistory();

    /**
     * Sets the terms and conditions accepted flag.
     *
     * @param bool $flag
     * @return self
     */
    public function setTermsAccepted($flag);

    /**
     * Gets the terms and conditions accepted flag.
     *
     * @return bool
     */
    public function getTermsAccepted();

    /**
     * Sets a reference for a job posting, used by the
     * organisation offering the job.
     *
     * @param string $reference
     */
    public function setReference($reference);
    
    /**
     * Gets a reference for a job posting, used by the
     * organisation offering the job.
     *
     * @return string $reference
     */
    public function getReference();

    /**
     * Sets the list of channels where a job opening should be published
     *
     * @param Array $portals
     */
    public function setPortals(array $portals);

    /**
     * Gets the list of channels where the job opening should be published
     *
     * @return Array
     */
    public function getPortals();

}