<?php

namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\Hydrator;
use Applications\Entity\History;
use Applications\Entity\HistoryCollection;
use Applications\Entity\HistoryRelationCollection;
use \Applications\Repository\Hydrator\Strategy\StatusNameStrategy;

class JsonHistoryBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $hydrator = new Hydrator\EntityHydrator();
        $hydrator->addStrategy('date', new Hydrator\DatetimeStrategy(
            Hydrator\DatetimeStrategy::FORMAT_MONGO,
            Hydrator\DatetimeStrategy::FORMAT_ISO
        ));
        $hydrator->addStrategy('status', new StatusNameStrategy());
        
        $builder = new HistoryBuilder($hydrator, new History(), new HistoryCollection());
       
        $builder->setRelation(new HistoryRelationCollection(
            array($serviceLocator->getServiceLocator()->get('mappers')->get('application'), 'fetchHistory')
        ), 'id');
        $builder->setReverseOrder(false);
        
        return $builder;        
    }
    
        
}