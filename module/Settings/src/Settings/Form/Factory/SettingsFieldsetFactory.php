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

use Interop\Container\ContainerInterface;
use Settings\Form\SettingsFieldset;
use Zend\ServiceManager\Factory\FactoryInterface;

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
        /* @var SettingsFieldset $ob */
        $class = class_exists($requestedName) ? $requestedName : SettingsFieldset::class;
    	$ob = new $class($container->get('FormElementManager'));
    	$ob->setName($requestedName);
        return $ob;
    }
}
