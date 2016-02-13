<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Form;

use Jobs\Form\BaseFieldset;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the BaseFieldset (Job Title and Location)
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class BaseFieldsetFactory implements FactoryInterface
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
        $fs = new BaseFieldset();
        $fs->setLocationEngineType($options->getPlugin());
        return $fs;
    }
}
