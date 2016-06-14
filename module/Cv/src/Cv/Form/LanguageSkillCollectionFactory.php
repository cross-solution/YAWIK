<?php

namespace Cv\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageSkillCollectionFactory implements FactoryInterface
{
    
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $container = new \Core\Form\CollectionContainer('Cv/LanguageSkillForm', new \Cv\Entity\Language());
        $container->setLabel(/*@translate */ 'Additional Language Skills');
        
		return $container;
    }
}
