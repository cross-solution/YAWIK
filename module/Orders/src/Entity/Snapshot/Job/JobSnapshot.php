<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Entity\Snapshot\Job;

use Core\Entity\EntityInterface;
use Core\Entity\EntityTrait;
use Core\Exception\ImmutablePropertyException;
use Jobs\Entity\JobInterface;
use Orders\Entity\Snapshot\SnapshotInterface;
use Orders\Entity\Snapshot\SnapshotTrait;
use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class JobSnapshot implements EntityInterface, SnapshotInterface
{
    use EntityTrait, SnapshotTrait;

    /**
     * Title of the job
     *
     * @ODM\String
     * @var string
     */
    protected $title;

    /**
     * Reference number
     *
     * @ODM\String
     * @var string
     */
    protected $reference;

    /**
     * Language of the job
     *
     * @ODM\String
     * @var string
     */
    protected $language;

    /**
     * Uri
     *
     * @ODM\String
     * @var string
     */
    protected $link;

    /**
     * Publish start date.
     *
     * @ODM\Field(type="tz_date")
     * @var \DateTime
     */
    protected $datePublishStart;

    /**
     * Apply uri
     *
     * @ODM\String
     * @var string
     */
    protected $uriApply;

    /**
     * Apply id
     *
     * @ODM\String
     * @var string
     */
    protected $applyId;

    /**
     * Publisher uri
     *
     * @ODM\String
     * @var string
     */
    protected $uriPublisher;

    /**
     * Organization name
     *
     * @ODM\String
     * @var string
     */
    protected $organizationName;

    /**
     * Name of the parent of the organization.
     * Only set, if organization is a hiring organization
     *
     * @ODM\String
     * @var string
     */
    protected $organizationParent;

    /**
     * Array of locations.
     *
     * @ODM\Field(type="collection")
     * @var array[]
     */
    protected $locations;

    /**
     * AtsMode
     *
     * @ODM\Field(type="hash")
     * @var array
     */
    protected $atsMode;


    /**
     * Gets the original job, if it still exists.
     *
     * @uses getEntity()
     * @return EntityInterface|null
     */
    public function getJob()
    {
        return $this->getEntity();
    }

    /**
     * Sets the original job.
     *
     * @param JobInterface $job
     *
     * @uses setEntity()
     * @return self
     * @throws ImmutablePropertyException
     */
    public function setJob(JobInterface $job)
    {
        $this->setEntity($job);
    }

    /**
     * Does the original job still exists?
     *
     * @uses hasEntity()
     * @return bool
     */
    public function hasJob()
    {
        return $this->hasEntity();
    }

    /**
     * @param string $applyId
     *
     * @return self
     */
    public function setApplyId($applyId)
    {
        $this->applyId = $applyId;

        return $this;
    }

    /**
     * @return string
     */
    public function getApplyId()
    {
        return $this->applyId;
    }

    /**
     * @param array $atsMode
     *
     * @return self
     */
    public function setAtsMode($atsMode)
    {
        $this->atsMode = $atsMode;

        return $this;
    }

    /**
     * @return array
     */
    public function getAtsMode()
    {
        return $this->atsMode;
    }

    /**
     * @param \DateTime $datePublishStart
     *
     * @return self
     */
    public function setDatePublishStart($datePublishStart)
    {
        $this->datePublishStart = $datePublishStart;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatePublishStart()
    {
        return $this->datePublishStart;
    }

    /**
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $link
     *
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param \array[] $locations
     *
     * @return self
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;

        return $this;
    }

    /**
     * @return \array[]
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param string $organizationName
     *
     * @return self
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * @param string $organizationParent
     *
     * @return self
     */
    public function setOrganizationParent($organizationParent)
    {
        $this->organizationParent = $organizationParent;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationParent()
    {
        return $this->organizationParent;
    }

    /**
     * @param string $reference
     *
     * @return self
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $uriApply
     *
     * @return self
     */
    public function setUriApply($uriApply)
    {
        $this->uriApply = $uriApply;

        return $this;
    }

    /**
     * @return string
     */
    public function getUriApply()
    {
        return $this->uriApply;
    }

    /**
     * @param string $uriPublisher
     *
     * @return self
     */
    public function setUriPublisher($uriPublisher)
    {
        $this->uriPublisher = $uriPublisher;

        return $this;
    }

    /**
     * @return string
     */
    public function getUriPublisher()
    {
        return $this->uriPublisher;
    }






}