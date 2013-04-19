<?php

namespace Core\Mapper\Criteria\Converter;

use Core\Mapper\Criteria\CriteriaInterface;

class MongoDb
{
    public function convert(CriteriaInterface $criteria)
    {
        $result = array();
        $and = $criteria->getCriteria();
        $or = $criteria->getOrCriteria();
        
        $andCount = count($and);
        $orCount = count($or);
        
        if ($orCount) {
            if ($andCount) {
                $result['$or'] = array(
                    array('$and' => $this->convertRecursive($and, true)),
                    $this->convertRecursive($or)
                );
            } else {
                $result['$or'] = $this->convertRecursive($or, true);
            }
        } else if ($andCount) {
            $result = $this->convertRecursive($and);
        } else {
            $result = array();
        }
        
        return $result;
    }
    
    protected function convertRecursive(array $criteria, $wrapCriterions=false)
    {
        $result = array();
        foreach ($criteria as $criterion) {
            if ($criterion instanceOf CriteriaInterface) {
                $result[] = $this->convert($criterion);
            } else {
                switch ($criterion->getMode()) {
                    case 'EQUALS':
                    default:
                        if (!$wrapCriterions) {
                            $result[$criterion->getProperty()] = $criterion->getValue();
                        } else {
                            $result[] = array($criterion->getProperty() => $criterion->getValue());
                        }
                        break;
                }
            }
        }
        return $result;
    }
}