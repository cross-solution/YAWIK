<?php

namespace Core\Repository\EntityBuilder;


use Core\Entity\EntityInterface;
use Core\Entity\RelationCollectionInterface;

class AggregateBuilder extends EntityBuilder
{
    protected $builders = array();
    
    public function addBuilder($property, $builder, $buildAsCollection = false)
    {
        $this->builders[$property] = array(
            'builder' => $builder,
            'asCollection' => $buildAsCollection
        );
        return $this;
    }
    
    public function getBuilder($property, $asSpec=false)
    {
        if (isset($this->builders[$property])) {
            return $asSpec ? $this->builders[$property] : $this->builders[$property]['builder'];
        }
        return null;
    }
    
    public function build($data = array())
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            die (__METHOD__.': Expects $data to be an array or implements Traversable.');
        }
        
        $injectRelations = array();
        foreach ($this->builders as $property => $builderSpec) {
            $builder = $builderSpec['builder'];
            $asCollection = $builderSpec['asCollection'];
            if (isset($data[$property])) {
                $data[$property] = $asCollection 
                                 ? $builder->buildCollection($data[$property])
                                 : $builder->build($data[$property]);
            } else if ($builder instanceOf RelationAwareInterface) {
                $injectRelations[$property] = $builder;
            }
        }
        
        
        $entity = parent::build($data);
        
        foreach ($injectRelations as $property => $builder) {
            $entity->{"set$property"}($builder->getRelation($entity));
        }
        
        return $entity;
    }
    
    public function unbuild(EntityInterface $entity)
    {
        $data = parent::unbuild($entity);
        
        foreach ($this->builders as $property => $builderSpec) {
            if (isset($data[$property])) {
                if ($data[$property] instanceOf RelationCollectionInterface && !$data[$property]->isCollectionLoaded()) {
                    $data[$property] = null;
                    continue;
                } 
                $data[$property] = $builderSpec['asCollection']
                                 ? $builderSpec['builder']->unbuildCollection($data[$property])
                                 : $builderSpec['builder']->unbuild($data[$property]);
            }
        }
        return $data;
    }
}