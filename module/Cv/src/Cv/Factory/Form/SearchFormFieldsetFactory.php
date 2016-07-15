<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Cv\Factory\Form;


use Cv\Form\SearchFormFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SearchFormFieldsetFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */

        $services = $serviceLocator->getServiceLocator();

        /* @var \Geo\Options\ModuleOptions $options */
        $options = $services->get('Geo/Options');
        $fs = new SearchFormFieldset(null, ['location_engine_type' => $options->getPlugin()]);
        return $fs;

    }
}