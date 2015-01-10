<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** StatusInterface.php */
    
namespace Jobs\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Application status entity
 *
 * @ODM\EmbeddedDocument
 */
class Status extends AbstractEntity implements StatusInterface
{
    /**
     * status values
     */
    protected static $orderMap = array(
        self::CREATED => 10,
        self::WAITING_FOR_APPROVAL => 20,
        self::REJECTED => 30,
        self::PUBLISH => 40,
        self::ACTIVE => 50,
        self::INACTIVE => 60,
        self::EXPIRED => 70,
    );

    /**
     * name of the job status
     * 
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * integer for ordering states.
     * 
     * @var string
     * @ODM\String
     */
    protected $order;

    public function __construct($status = self::CREATED)
    {
        $constant = 'self::' . strtoupper($status);
        if (!defined($constant)) {
            throw new \DomainException('Unknown status: ' . $status);
        }
        $this->name=constant($constant);
        $this->order=$this->getOrder();
    }

    /**
     * @see \Jobs\Entity\StatusInterface::getName()
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @see \Jobs\Entity\StatusInterface::getOrder()
     * @return Int
     */
    public function getOrder()
    {
        return self::$orderMap[$this->getName()];
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getStates()
    {
        $states = self::$orderMap;
        asort($states, SORT_NUMERIC);
        return array_keys($states);
    }
}