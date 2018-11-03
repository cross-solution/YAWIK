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

use Core\Entity\EntityInterface;

/**
 * Interface of applications facts entity
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface FactsInterface extends EntityInterface
{

    /**
     * Sets the expected salary.
     *
     * @param string $salary
     *
     * @return self
     */
    public function setExpectedSalary($salary);

    /**
     * Gets the expected salary.
     *
     * @return string
     */
    public function getExpectedSalary();

    /**
     * Sets the willingness to travel
     *
     * @param $willingnessToTravel
     * @return $this|FactsInterface
     */
    public function setWillingnessToTravel($willingnessToTravel);


    /**
     * Gets the willingness to travel
     *
     * @return string
     */
    public function getWillingnessToTravel();


    /**
     * sets the earliest starting date
     *
     * @param $earliestStartingDate
     * @return $this|FactsInterface
     */
    public function setEarliestStartingDate($earliestStartingDate);

    /**
     * Gets the earliest starting date.
     *
     * @return string
     */
    public function getEarliestStartingDate();

    /**
     * sets the driving license
     *
     * @param drivingLicense
     * @return $this|FactsInterface
     */
    public function setDrivingLicense($drivingLicense);

    /**
     * Gets the driving license.
     *
     * @return string
     */
    public function getDrivingLicense();
}
