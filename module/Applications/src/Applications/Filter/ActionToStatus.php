<?php

namespace Applications\Filter;

use Zend\Filter\FilterInterface;
use Applications\Entity\StatusInterface as Status;

class #ActionToStatus implements FilterInterface
{

    protected $actionToStatusMap = array(
        'confirm' => Status::CONFIRMED,
        'invite' => Status::INVITED,
        'reset' => Status::INCOMING,
    );
    
    public function filter($value)
    {
        return isset($this->actionToStatusMap[$value])
            ? $this->actionToStatusMap[$value]
            : false;
    }
}