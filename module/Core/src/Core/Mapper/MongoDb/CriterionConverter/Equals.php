<?php

namespace Core\Mapper\MongoDb\CriterionConverter;

use Core\Mapper\Query\Criteria\Criterion\CriterionConverterInterface;
use Core\Mapper\Query\Criteria\Criterion\CriterionInterface;

class Equals implements CriterionConverterInterface
{

    public function convert(CriterionInterface $criterion)
    {
        return array($criterion->getProperty() => $criterion->getValue());
    }
}