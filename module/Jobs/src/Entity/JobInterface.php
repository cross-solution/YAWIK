<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Jobs\Entity;

use Core\Entity\MetaDataProviderInterface;
use Core\Entity\AttachableEntityInterface;
use Organizations\Entity\OrganizationInterface;
use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Auth\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;
use Core\Entity\ModificationDateAwareEntityInterface;
use Core\Entity\PermissionsAwareInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * Interface for a Job Posting
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @package Jobs\Entity
 */
interface JobInterface extends
    EntityInterface,
    IdentifiableEntityInterface,
    ModificationDateAwareEntityInterface,
    PermissionsAwareInterface,
    ResourceInterface,
    MetaDataProviderInterface,
    AttachableEntityInterface
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
     * checks, weather a job is enabled for getting applications
     * @deprecated since 0.19 - Use atsMode sub document via getAtsMode()
     * @return boolean
     */
    public function getAtsEnabled();

    /**
     * enables a job add to receive applications
     *
     * @param boolean $atsEnabled
     * @deprecated since 0.19 - Use atsMode entity via setAtsMode()
     * @return \Jobs\Entity\Job
     */
    public function setAtsEnabled($atsEnabled);

    /**
     * Sets the ATS mode.
     *
     * @param AtsMode $mode
     *
     * @return self
     * @since 0.19
     */
    public function setAtsMode(AtsMode $mode);

    /**
     * Gets the ATS mode.
     *
     * @return AtsMode
     * @since 0.19
     */
    public function getAtsMode();

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
     * @return \DateTime
     */
    public function getDatePublishStart();
    
    /**
     * Sets the publishing date of a job posting
     *
     * @param $datePublishStart
     * @return $this
     */
    public function setDatePublishStart($datePublishStart);


    /**
     * Gets the end date for publishing a job posting
     *
     * @return \DateTime
     */
    public function getDatePublishEnd();

    /**
     * Sets the end date for publishing a job posting
     *
     * @param $datePublishEnd
     * @return $this
     */
    public function setDatePublishEnd($datePublishEnd);
    
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
     * Gets the organisation name, which offers the job posting
     *
     * @return string
     */
    public function getCompany();

    /**
     * Sets the organisation name, which offers a job posting
     *
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
     * @return StatusInterface|null
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

    /**
     * @param \Jobs\Entity\Classifications $classifications
     *
     * @return self
     */
    public function setClassifications($classifications);

    /**
     * @return \Jobs\Entity\Classifications
     */
    public function getClassifications();

    /**
     * Gets the Values of a job template
     *
     * @return TemplateValues
     */
    public function getTemplateValues();


    /**
     * @param EntityInterface $templateValues
     *
     * @return $this
     */
    public function setTemplateValues(EntityInterface $templateValues = null);
}
