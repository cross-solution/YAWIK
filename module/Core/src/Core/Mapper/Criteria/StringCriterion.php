<?php

namespace Core\Mapper\Criteria;

class StringCriterion extends Criterion
{
    const EQUALS="EQUALS";
    
    protected $mode;
    
    public function equals($value)
    {
        $this->setValue((string) $value);
        $this->mode = self::EQUALS;
        return $this;
    }
    
    public function toArray()
    {
        return array(
            'property' => $this->property,
            'value' => $this->value,
            'mode' => $this->mode,
        );
    }
}