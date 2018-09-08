<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class PreferredJob extends AbstractIdentifiableEntity implements PreferredJobInterface
{
    /**
     * @var array
     * @ODM\Field(type="collection")
     */
    protected $typeOfApplication = array();

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $desiredJob;
    
    /**
     * @var string
     * @ODM\Field(type="string")
     **/
    protected $desiredLocation;

    /**
     * @var Collection
     * @ODM\EmbedMany(targetDocument="\Cv\Entity\Location")
     **/
    protected $desiredLocations;


    /**
     * willingness to travel,
     *
     * yes, no, bedingt
     *
     * @ODM\Field(type="string") */
    protected $willingnessToTravel;


    /** expectedSalary
     * @ODM\Field(type="string") */
    protected $expectedSalary;

    public function __construct()
    {
        $this->desiredLocations = new ArrayCollection();
    }

    /**
     * Apply for a job, internship or studies
     *
     * @param   array $typeOfApplication
     * @return  $this
     */
    public function setTypeOfApplication(array $typeOfApplication)
    {
        $this->typeOfApplication = $typeOfApplication;
        return $this;
    }

    /**
     * Gets the type of an Application
     *
     * @return array
     */
    public function getTypeOfApplication()
    {
        return $this->typeOfApplication;
    }
    
    public function setDesiredJob($desiredJob)
    {
        $this->desiredJob=$desiredJob;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesiredJob()
    {
        return $this->desiredJob;
    }

    /**
     * @param $desiredLocation
     *
     * @return $this
     */
    public function setDesiredLocation($desiredLocation)
    {
        $this->desiredLocation=$desiredLocation;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesiredLocation()
    {
        return $this->desiredLocation;
    }

    /**
     * @param Collection $desiredLocations
     *
     * @return $this
     */
    public function setDesiredLocations(Collection $desiredLocations)
    {
        $this->desiredLocations = $desiredLocations;
        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getDesiredLocations()
    {
        return $this->desiredLocations;
    }

    /**
     * @param $willingnessToTravel
     *
     * @return $this
     */
    public function setWillingnessToTravel($willingnessToTravel)
    {
        $this->willingnessToTravel=$willingnessToTravel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWillingnessToTravel()
    {
        return $this->willingnessToTravel;
    }

    /**
     * @param $expectedSalary
     *
     * @return $this
     */
    public function setExpectedSalary($expectedSalary)
    {
        $this->expectedSalary=$expectedSalary;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpectedSalary()
    {
        return $this->expectedSalary;
    }
}
