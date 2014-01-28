<?php

namespace Applications\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 *
 * @author cbleek
 *
 * @ODM\EmbeddedDocument
 */
class Status extends AbstractEntity implements StatusInterface
{
    protected static $orderMap = array(
        self::INCOMING => 10,
        self::CONFIRMED => 20,
        self::INVITED => 30,
        self::REJECTED => 40,
    );

    /**
     * name of the status
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * 
     * @var string
     * @ODM\String
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

    public function getName()
    {
        return $this->name;
    }

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