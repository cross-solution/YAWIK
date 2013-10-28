<?php

namespace Cv\Repository\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Cv\Repository\Builder\CvBuilder;
use Cv\Repository\Mapper\CvMapper;
use Core\Repository\Hydrator;

class CvMapperFactory implements FactoryInterface
{
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        
        
        
        $db = $serviceLocator->getServiceLocator()->get('MongoDb');
//         $cvBuilder = $serviceLocator->get('CvBuilder');
//         $educationBuilder = $serviceLocator->get('EducationBuilder');
//         $employmentBuilder = $serviceLocator->get('EmploymentBuilder');
        
        $mapper = new CvMapper($db->cvs);
//         $mapper->setCvBuilder($cvBuilder)
//                ->setEducationBuilder($educationBuilder)
//                ->setEmploymentBuilder($employmentBuilder);
        
        return $mapper;
        
    }

    
}