<?php

namespace Cv\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\EntityBuilder\AggregateBuilder as Builder;
use Core\Repository\Hydrator;

class CvBuilderFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $educationsBuilder = $serviceLocator->get('education');
        $employmentsBuilder = $serviceLocator->get('employment');
//         $buildMode = Hydrator\SubDocumentsStrategy::AS_COLLECTION;
        
        $hydrator = new Hydrator\EntityHydrator();
//         $hydrator->addStrategy('educations', new Hydrator\SubDocumentsStrategy(
//             $educationsBuilder, $buildMode
//         ));
//         $hydrator->addStrategy('employments', new Hydrator\SubDocumentsStrategy(
//             $employmentsBuilder, $buildMode
//         ));
        
        
        
        $builder = new Builder(
            $hydrator, 
            new \Cv\Entity\Cv(),
            new \Core\Entity\Collection()
        );
        
        $builder->addBuilder('educations', $educationsBuilder, /*asCollection*/ true)
                ->addBuilder('employments', $employmentsBuilder, true);
        
        return $builder;
        
    }

    
}