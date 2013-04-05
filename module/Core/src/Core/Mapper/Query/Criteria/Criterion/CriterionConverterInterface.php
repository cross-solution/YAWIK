<?php

namespace Core\Mapper\Query\Criteria\Criterion;

interface CriterionConverterInterface
{
    public function convert(CriterionInterface $criterion);
}