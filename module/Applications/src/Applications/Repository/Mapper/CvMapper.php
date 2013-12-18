<?php

namespace Applications\Repository\Mapper;

use Cv\Repository\Mapper\CvMapper as BaseMapper;
use Core\Entity\EntityInterface;
use Core\Repository\EntityBuilder\EntityBuilderInterface;

class CvMapper extends BaseMapper
{

    
    protected function getBuilder($builder)
    {
        if (!$builder instanceOf EntityBuilderInterface) {
            $builder = $this->builders->get("application-cv-$builder");
        }
        return $builder;
    }
    
    protected function getMongoFields(array $fields, $exclude = false)
    {
        $mongoFields = array();
        foreach ($fields as $key => $value) {
            if (is_numeric($key)) {
                $value="cv.$value";
                $mongoFields[$value] = !$exclude;
            } else {
                $key = "cv.$key";
                $mongoFields[$key] = $value;
            }
        }
        return $mongoFields;
    }
    
    protected function getData($query=array(), array $fields = array(), $exclude = false)
    {
        if (!is_array($query)) {
            $query = array('cv._id' => $this->getMongoId($query)); 
        } 
        $data = parent::getData($query, $fields, $exclude);
        if (!isset($data['cv'])) { return array(); }
        
//         $result = $data;
//         foreach (explode(".", $this->prefix) as $key) {
//             $result = $result[$key];
//         }
        return $data['cv'];
    }

    public function save(EntityInterface $entity)
    {
        die (__METHOD__.': Save is disallowed in this mapper. Use Applications-Mapper.');
    }
    
} 