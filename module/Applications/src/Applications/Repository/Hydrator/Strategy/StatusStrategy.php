<?php

namespace Applications\Repository\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use Applications\Entity\Status;

class StatusStrategy implements StrategyInterface
{

    public function hydrate($value)
    {
        if ($value instanceOf Status) {
            return $value;
        }
        
        $status = new Status($value['name']);
        return $status;
    }
    
    public function extract($value)
    {
        return array(
            'name' => $value->getName(),
            'order' => $value->getOrder(),
        );
    }
}