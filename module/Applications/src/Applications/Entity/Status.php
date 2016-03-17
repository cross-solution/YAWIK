<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** StatusInterface.php */
    
namespace Applications\Entity;

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
    protected static $orderMap = [
        self::INCOMING => 10,
        self::CONFIRMED => 20,
        self::ACCEPTED =>25,
        self::INVITED => 30,
        self::REJECTED => 40,
    ];

    /**
     * name of the status
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * integer for ordering states.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $order;

    public function __construct($status = self::INCOMING)
    {
        $constant = 'self::' . strtoupper($status);
        if (!defined($constant)) {
            throw new \DomainException('Unknown status: ' . $status);
        }
        $this->name=constant($constant);
        $this->order=$this->getOrder();
    }

    /**
     * @see \Applications\Entity\StatusInterface::getName()
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @see \Applications\Entity\StatusInterface::getOrder()
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
