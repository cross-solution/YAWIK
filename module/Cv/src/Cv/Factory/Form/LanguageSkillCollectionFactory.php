<?php

namespace Cv\Factory\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageSkillCollectionFactory implements FactoryInterface
{
    /**
     * Create a CollectionContainer form
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return CollectionContainer
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $collectionContainer = new \Core\Form\CollectionContainer('Cv/LanguageSkillForm', new \Cv\Entity\Language());
        $collectionContainer->setLabel(/*@translate */ 'Additional Language Skills');
        return $collectionContainer;
    }

    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
    */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), "");
    }
}
