<?php

namespace Applications\Repository\EntityBuilder;



use Core\Repository\Hydrator\DatetimeStrategy;
use Core\Entity\Hydrator\EntityHydrator;
use Zend\ServiceManager\ServiceLocatorInterface;

class JsonApplicationBuilderFactory extends ApplicationBuilderFactory
{
	
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $builder = parent::createService($serviceLocator);
        $builder->setExtractRelations(true, /*recursive*/ true);
        return $builder;
    }
    
    protected function getHydrator()
    {
        $hydrator = new EntityHydrator();
        $strategy = new DatetimeStrategy(DatetimeStrategy::FORMAT_MONGO, DatetimeStrategy::FORMAT_ISO);
        $hydrator->addStrategy('dateCreated', $strategy)
                 ->addStrategy('dateModified', $strategy);
        return $hydrator;
    }
    
    protected function getBuilderName($builderName)
    {
        return 'json-' . $builderName;
    }

    
}