<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013-2014 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Entity;

/**
 * Interface of applications facts entity
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de> 
 */
interface FactsInterface 
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
    
}