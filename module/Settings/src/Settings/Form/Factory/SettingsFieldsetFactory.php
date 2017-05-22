<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Settings\Form\Factory;

use Settings\Form\SettingsFieldset;
use Interop\Container\ContainerInterface;
use Zend\Form\FormElementManager\FormElementManagerV2Polyfill;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface; 

/**
 * Factory for \Settings\Form\SettingsFieldset
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.30
 * @todo write test  
 */
class SettingsFieldsetFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!$container instanceOf FormElementManagerV2Polyfill) {
            $container = $container->get('FormElementManager');
        }

        return new $requestedName($container);
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator, $name=null, $requestedName=SettingsFieldset::class)
    {
        return $this($serviceLocator, $requestedName);
    }
}
