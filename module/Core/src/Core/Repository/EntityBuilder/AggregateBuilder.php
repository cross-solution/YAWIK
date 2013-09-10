<?php

namespace Core\Repository\EntityBuilder;


use Core\Entity\EntityInterface;
use Core\Entity\RelationInterface;
use Core\Entity\RelationEntity;

class AggregateBuilder extends EntityBuilder
{
    protected $builders = array();
    protected $extractRelations = false;
    
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
    
    public function setExtractRelations($flag, $recursive = false)
    {
        $this->extractRelations = (bool) $flag;
        if ($recursive) {
            foreach ($this->builders as $builderSpec) {
                $builderSpec['builder']->setExtractRelations($flag);
            }
        }
        return $this;
    }
    
    public function extractRelations() 
    {
        return $this->extractRelations;    
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
                if ($data[$property] instanceOf RelationInterface) {
                    if ($this->extractRelations || $data[$property]->isLoaded()) {
                
                        if ($data[$property] instanceOf RelationEntity) {
                            $data[$property] = $data[$property]->getEntity();
                            if (method_exists($data[$property], 'getId') && null == $data[$property]->getId()) {
                                unset($data[$property]);
                                continue;
                            }
                        }
                    } else {
                        unset($data[$property]);
                        continue;
                    }
                } 
                $data[$property] = $builderSpec['asCollection']
                                 ? $builderSpec['builder']->unbuildCollection($data[$property])
                                 : $builderSpec['builder']->unbuild($data[$property]);
            }
        }
        return $data;
    }
}