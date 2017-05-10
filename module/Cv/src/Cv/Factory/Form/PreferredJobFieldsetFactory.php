<?php
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 16/06/16
 * Time: 0:36
 */

namespace Cv\Factory\Form;


use Cv\Form\PreferredJobFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class PreferredJobFieldsetFactory implements FactoryInterface
{

    /**
     * Create a PreferredJobFieldset form
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
        /* @var \Geo\Options\ModuleOptions $options */
        $options = $container->get('Geo/Options');
        $fs = new PreferredJobFieldset();
        $fs->setLocationEngineType($options->getPlugin());
        return $fs;
    }

    /**
     * Creates Preferred Job Form
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return PreferredJobFieldset
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */
        return $this($serviceLocator->getServiceLocator(), PreferredJobFieldset::class);
    }

}