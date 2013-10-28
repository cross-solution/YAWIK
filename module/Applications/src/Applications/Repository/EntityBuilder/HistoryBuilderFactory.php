<?php

namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\Hydrator;
use Applications\Entity\History;
use Applications\Entity\HistoryCollection;
use Applications\Entity\HistoryRelationCollection;

class HistoryBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $hydrator = new Hydrator\EntityHydrator();
        $hydrator->addStrategy('date', new Hydrator\DatetimeStrategy())
                 ->addStrategy('status', new \Applications\Repository\Hydrator\Strategy\StatusStrategy());
        
        $builder = new HistoryBuilder($hydrator, new History(), new HistoryCollection());
        
       
        $builder->setRelation(new HistoryRelationCollection(
            array($serviceLocator->getServiceLocator()->get('mappers')->get('application'), 'fetchHistory')
        ), 'id');
        
        return $builder;        
    }
    
        
}