<?php

namespace Applications\Repository\EntityBuilder;



use Core\Repository\Hydrator\DatetimeStrategy;
use Core\Entity\Hydrator\EntityHydrator;

class JsonApplicationBuilderFactory extends ApplicationBuilderFactory
{
	
    
    protected function getHydrator()
    {
        $hydrator = new EntityHydrator();
        $strategy = new DatetimeStrategy(DatetimeStrategy::FORMAT_MONGO, DatetimeStrategy::FORMAT_ISO);
        $hydrator->addStrategy('dateCreated', $strategy)
                 ->addStrategy('dateModified', $strategy);
        return $hydrator;
    }

    
}