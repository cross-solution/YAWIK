<?php

namespace Auth\Entity\Filter;

use Zend\Filter\FilterInterface;

class CredentialFilter implements FilterInterface
{
    
    public function filter($value)
    {
        $weird = sha1(md5($value) . md5(strrev($value)));
        return $weird;
    }
    
}