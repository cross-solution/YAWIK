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


    public function setExpectedSalary($salary)
    {
        $this->expectedSalary = $salary;

        return $this;
    }

    public function getExpectedSalary()
    {
        return $this->expectedSalary;
    }
    
}