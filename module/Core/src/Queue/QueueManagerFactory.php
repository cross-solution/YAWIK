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
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Queue\QueueManager
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test  
 */
class QueueManagerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
        $dm      = $container->get('Core/DocumentManager');
        $client  = $dm->getConnection()->getMongoClient();
        $config  = $container->get('config');
        $config  = isset($config['queues']) ? $config['queues'] : [];
        $service = new QueueManager($client, $config);
        
        return $service;    
    }
}
