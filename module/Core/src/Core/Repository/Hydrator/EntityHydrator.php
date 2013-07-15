<?php

namespace Core\Repository\Hydrator;

class EntityHydrator extends \Core\Entity\Hydrator\EntityHydrator
{
    
    public function extract($object)
    {
        $data = parent::extract($object);
        if (isset($data['id']) && $data['id']) {
            $data['_id'] = new \MongoId($data['id']);
        }
        unset($data['id']);
        $data = array_filter($data, function($item) { return null !== $item; });
        
        return $data;
    }
    
    public function hydrate(array $data, $object)
    {
        if (isset($data['_id'])) {
            $data['id'] = (string) $data['_id'];
            unset($data['_id']);
        }
        return parent::hydrate($data, $object);
    }
    
    
}