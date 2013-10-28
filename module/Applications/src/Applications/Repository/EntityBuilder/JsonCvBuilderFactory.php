<?php

namespace Applications\Repository\EntityBuilder;

use Core\Entity\RelationEntity;

class JsonCvBuilderFactory extends CvBuilderFactory
{
	
    protected function getHydrator()
    {
        return new \Core\Entity\Hydrator\EntityHydrator();
    }
        
}