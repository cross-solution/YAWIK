<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;

use Jobs\Form\ListFilterLocationFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the ListFilterLocation (Job Title and Location)
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class ListFilterLocationFieldsetFactory implements FactoryInterface
{
    /**
     * Creates the multiposting select box.
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */

        $services = $serviceLocator->getServiceLocator();
        /* @var \Geo\Options\ModuleOptions $options */
        $options = $services->get('Geo/Options');
        $fs = new ListFilterLocationFieldset(['location_engine_type' => $options->getPlugin()]);
        return $fs;
    }
}
