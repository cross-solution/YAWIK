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

class PreferredJobFieldsetFactory implements FactoryInterface
{
    /**
     * Creates Preferred Job Form
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return PreferredJobFieldset
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */

        $services = $serviceLocator->getServiceLocator();
        /* @var \Geo\Options\ModuleOptions $options */
        $options = $services->get('Geo/Options');
        $fs = new PreferredJobFieldset();
        $fs->setLocationEngineType($options->getPlugin());
        return $fs;
    }

}