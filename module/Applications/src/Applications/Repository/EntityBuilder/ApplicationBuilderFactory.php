<?php

namespace Applications\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\EntityBuilder\AggregateBuilder as Builder;
use Core\Repository\Hydrator;

class ApplicationBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $cvBuilder = $serviceLocator->get('cv');
        
        $hydrator = $this->getHydrator();

        $builder = new Builder(
            $hydrator, 
            new \Applications\Entity\Application(),
            new \Core\Entity\Collection()
        );
        
        $builder->addBuilder('cv', $cvBuilder);
        
        return $builder;
        
    }
    
    protected function getHydrator()
    {
        $hydrator = new Hydrator\EntityHydrator();
        $hydrator->addStrategy('dateCreated', new Hydrator\DatetimeStrategy())
                 ->addStrategy('dateModified', new Hydrator\DatetimeStrategy());
        return $hydrator;
    }

    
}