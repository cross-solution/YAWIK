<?php

namespace Cv\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SkillCollectionFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = new \Core\Form\CollectionContainer('CvSkillForm', new \Cv\Entity\Skill());
        $container->setLabel(/*@translate */ 'Skills');
        
		return $container;
    }
}
