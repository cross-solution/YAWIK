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

use Core\Queue\Worker\MongoWorker;
use Interop\Container\ContainerInterface;
use SlmQueue\Queue\QueuePluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Queue\Controller\MongoQueueController
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class MongoQueueControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $service = new MongoQueueController(
            $container->get(MongoWorker::class),
            $container->get(QueuePluginManager::class)
        );
        
        return $service;    
    }
}
