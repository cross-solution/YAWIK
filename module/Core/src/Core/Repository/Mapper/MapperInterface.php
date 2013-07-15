<?php

namespace Core\Repository\Mapper;

interface MapperInterface
{
    public function setCollection(\MongoCollection $collection);
    public function getCollection();
    
    public function find($id, array $fields=array());
    public function fetchAll(array $query = array(), array $fields = array());
    
    public function save(array $data);
}