<?php

namespace Applications\Repository\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Applications\Entity\Status;

class StatusStrategy implements StrategyInterface
{

    const EXTRACT_NAME   = 'EXTRACT_NAME';
    const EXTRACT_STATUS = 'EXTRACT_STATUS';
    
    protected $extractMode;
    
    public function __construct($extractMode = self::EXTRACT_STATUS)
    {
        $this->setExtractMode($extractMode);
    }
    
    public function setExtractMode($mode)
    {
        $this->extractMode = $mode;
        return $this;
    }
    
    public function getExtractMode()
    {
        return $this->extractMode;
    }
    
    public function hydrate($value)
    {
        $status = new Status($value);
        return $status;
    }
    
    public function extract($value)
    {
        if (!$value instanceOf Status) {
            throw new \InvalidArgumentException('Value must be of type Applications\\Entity\\Status');
        }
        
        return self::EXTRACT_NAME == $this->getExtractMode() 
               ? $value->getName() 
               : $value->getStatus();
    }
}