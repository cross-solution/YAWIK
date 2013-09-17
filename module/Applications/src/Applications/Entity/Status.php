<?php

namespace Applications\Entity;

use Core\Entity\AbstractEntity;

class Status extends AbstractEntity
{
    
    const STATUS_NEW = 0;
    const STATUS_CONFIRMED = 10;
    const STATUS_INVITED = 20;
    
    protected static $statusNames = array(
        self::STATUS_NEW        => 'new',
        self::STATUS_CONFIRMED  => 'confirmed',
        self::STATUS_INVITED    => 'invited',
    );
    
    protected $status;
        
    public function __construct($status = null)
    {
        if (null !== $status) {
            $this->setStatus($status);
        }
    }
    
    public function setStatus($status = self::STATUS_NEW)
    {
        $this->status = $status;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function getName()
    {
        return self::$statusNames[$this->getStatus()];
    }
    
    public function __toString()
    {
        return $this->getName();
    }
}