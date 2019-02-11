<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Controller;

use Interop\Container\ContainerInterface;
use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Queue\Controller\MongoQueueListController
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test  
 */
class MongoQueueListControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new MongoQueueListController(
            $container->get(QueuePluginManager::class)
        );
        
        return $service;    
    }
}
