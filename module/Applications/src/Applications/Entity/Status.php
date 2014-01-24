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
    protected static $order = array(
        self::INCOMING => 10,
        self::CONFIRMED => 20,
        self::INVITED => 30,
        self::REJECTED => 40,
    );
    
    /**
     * 
     * @var unknown
     * @ODM\String
     */
    protected $status;
        
    public function __construct($status = self::INCOMING)
    {
        $constant = 'self::' . strtoupper($status);
        if (!defined($constant)) {
            throw new \DomainException('Unknown status: ' . $status);
        }
        $this->status = constant($constant);
    }
    
    public function getName()
    {
        return $this->status;
    }

    public function getOrder()
    {
        return self::$order[$this->getName()];
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function getStates()
    {
        $states = self::$order;
        asort($states, SORT_NUMERIC);
        return array_keys($states);
    }
}