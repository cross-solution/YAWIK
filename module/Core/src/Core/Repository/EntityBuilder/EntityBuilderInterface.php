<?php

namespace Core\Repository\EntityBuilder;

use Zend\Stdlib\Hydrator\HydratorInterface;
use Core\Entity\EntityInterface;
use Core\Entity\CollectionInterface;

interface EntityBuilderInterface
{

    public function setHydrator(HydratorInterface $hydrator);
    public function getHydrator();
    
    public function setEntityPrototype(EntityInterface $entity);
    public function getEntity();
    
    public function setCollectionPrototype(CollectionInterface $collection);
    public function getCollection();
    
    public function build($data = array());
    public function unbuild(EntityInterface $entity);
    public function buildCollection($data);
    public function unbuildCollection(CollectionInterface $collection);

}