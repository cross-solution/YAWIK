<?php

namespace Core\Mapper\MongoDb;

use Core\Mapper\Query\AbstractConverter;
use Core\Mapper\Query\Criteria\CriteriaInterface;
use Core\Mapper\Query\Query;
use Core\Mapper\MapperInterface as CoreMapperInterface;

class QueryConverter extends AbstractConverter
{
    
    
    public function convert(Query $query, \Core\Mapper\MapperInterface $mapper)
    {
        $mongoQuery = $this->convertCondition($query->getCondition());
        $cursor = $mapper->getCollection()->find($mongoQuery);
        $cursor->sort($this->convertSort($query->getSort()));
        $limit = $query->getLimit();
        if ($limit) {
            $cursor->limit($limit->count)
                   ->skip($limit->offset);
        }
        return $cursor;
    }
    
    protected function convertCondition(CriteriaInterface $criteria)
    {
        return $this->convertCriteria($criteria);
    }
    
    protected function convertCriteria($criteria)
    {
        $result = array();
        
        
        $operator = $criteria->isChainAnd() ? '$and' : '$or';
        //print_R($criteria->getCriteria());
        foreach ($criteria->getCriteria() as $criterion) {
            $method = $criterion instanceOf CriteriaInterface
                    ? 'convertCriteria'
                    : 'convertCriterion';
            
            $result[$operator][] = $this->$method($criterion);
        }
        
        return $result;
    }
    
    protected function convertCriterion($criterion)
    {
        $nameParts = explode('\\', get_class($criterion));
        $name = array_pop($nameParts);
        $converter = $this->getCriterionConverterPluginManager()
                          ->get($name);
        
        return $converter->convert($criterion);
    }
    
    protected function convertSort($sort)
    {
        if (empty($sort)) {
            return null;
        }
        $result = array();
        foreach ($sort->getCollection() as $option) {
            $result[$option->property] = $option->ascending ? 1 : -1;
        }
        
        return $result;
    }
}