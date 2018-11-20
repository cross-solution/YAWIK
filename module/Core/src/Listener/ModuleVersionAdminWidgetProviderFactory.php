<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Listener;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Listener\ModuleVersionAdminWidgetProvider
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test  
 */
class ModuleVersionAdminWidgetProviderFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new ModuleVersionAdminWidgetProvider(
            $container->get('ModuleManager')
        );
        
        return $service;    
    }
}
