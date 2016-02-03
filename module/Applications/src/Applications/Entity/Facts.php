<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Application facts.
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Facts extends AbstractEntity implements FactsInterface
{
    /**
     * The expected salary.
     *
     * @ODM\String
     * @var string
     */
    protected $expectedSalary;

    /**
     * The willingness to travel
     *
     * @ODM\String
     * @var string
     */
    protected $willingnessToTravel;

    /**
     * The earliestStartingDate
     *
     * @ODM\String
     * @var string
     */
    protected $earliestStartingDate;

    /**
     * The drivingLicense
     *
     * @ODM\Boolean
     * @var bool
     */
    protected $drivingLicense;

    /**
     * @param string $salary
     * @return $this|FactsInterface
     */
    public function setExpectedSalary($salary)
    {
        $this->expectedSalary = $salary;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedSalary()
    {
        return $this->expectedSalary;
    }

    /**
     * @param $willingnessToTravel
     * @return $this|FactsInterface
     */
    public function setWillingnessToTravel($willingnessToTravel)
    {
        $this->willingnessToTravel = $willingnessToTravel;
        return $this;
    }

    /**
     * @return string
     */
    public function getWillingnessToTravel()
    {
        return $this->willingnessToTravel;
    }

    /**
     * @param $earliestStartingDate
     * @return $this|FactsInterface
     */
    public function setEarliestStartingDate($earliestStartingDate)
    {
        $this->earliestStartingDate = $earliestStartingDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getEarliestStartingDate()
    {
        return $this->earliestStartingDate;
    }

    /**
     * @param $drivingLicense
     * @return $this|FactsInterface
     */
    public function setDrivingLicense($drivingLicense)
    {
        $this->drivingLicense = $drivingLicense;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDrivingLicense()
    {
        return $this->drivingLicense;
    }
}
