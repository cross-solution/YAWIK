<?php

namespace Core\Mapper\MongoDb\CriterionConverter;

use Core\Mapper\Query\Criteria\Criterion\CriterionConverterInterface;
use Core\Mapper\Query\Criteria\Criterion\CriterionInterface;

class StartsWith implements CriterionConverterInterface
{

    public function convert(CriterionInterface $criterion)
    {
        return array($criterion->getProperty() => new \MongoRegex('/^' . $criterion->getValue() . '/'));
    }
}