<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue;

use Interop\Container\ContainerInterface;
use SlmQueue\Job\JobPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Queue\LazyJob
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test  
 */
class LazyJobFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new LazyJob($container->get(JobPluginManager::class), $options);
        
        return $service;    
    }
}
