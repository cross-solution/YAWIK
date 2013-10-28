<?php

namespace Core\Mapper\Query\Option;

interface OptionInterface
{
    
    public function getOptionName();
    public function setFromParams(array $params);
}