<?php

namespace Cv\Repository\EntityBuilder;

use Zend\ServiceManager\FactoryInterface;
use Core\Repository\EntityBuilder\AggregateBuilder as Builder;
use Core\Repository\Hydrator;
use Core\Repository\EntityBuilder\AbstractCopyableBuilderFactory;

class CvBuilderFactory extends AbstractCopyableBuilderFactory implements FactoryInterface
{
    
    
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $educationsBuilder = $serviceLocator->get($this->getBuilderName('education'));
        $employmentsBuilder = $serviceLocator->get($this->getBuilderName('employment'));
        $skillsBuilder = $serviceLocator->get($this->getBuilderName('skill'));
        
//         $buildMode = Hydrator\SubDocumentsStrategy::AS_COLLECTION;
        
        $hydrator = $this->getHydrator();
//         $hydrator->addStrategy('educations', new Hydrator\SubDocumentsStrategy(
//             $educationsBuilder, $buildMode
//         ));
//         $hydrator->addStrategy('employments', new Hydrator\SubDocumentsStrategy(
//             $employmentsBuilder, $buildMode
//         ));
        
        
        $builderClass = $this->getBuilderClass();
        $builder = new $builderClass(
            $hydrator, 
            new \Cv\Entity\Cv(),
            new \Core\Entity\Collection()
        );
        
        $builder->addBuilder('educations', $educationsBuilder, /*asCollection*/ true)
                ->addBuilder('employments', $employmentsBuilder, true)
                ->addBuilder('skills', $skillsBuilder, true);
                
        
        return $builder;
        
    }
    
    protected function getBuilderClass()
    {
        return '\\Core\\Repository\\EntityBuilder\\AggregateBuilder';
    }

    
}