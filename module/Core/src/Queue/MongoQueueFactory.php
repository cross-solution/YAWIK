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
 * Factory for \Core\Queue\MongoQueue
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class MongoQueueFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
        $config = $container->get('config');
        $config = $config['slm_queue']['queues'][$requestedName] ?? [];
        $dm = $container->get('Core/DocumentManager');

        // @codeCoverageIgnoreStart
        if (isset($config['dns'])) {
            $client = new \MongoDB\Client($config['dns']);
        } else {
            $client = $dm->getConnection()->getMongoClient()->getClient();
        }
        // @codeCoverageIgnoreEnd

        if (!isset($config['db'])) {
            $config['db'] = $dm->getConfiguration()->getDefaultDB();
        }

        if (!isset($config['collection'])) {
            $config['collection'] = 'core.queue';
        }

        $collection = $client->selectCollection($config['db'], $config['collection']);
        $jobPlugins = $container->get(JobPluginManager::class);

        $service = new MongoQueue($collection, $requestedName, $jobPlugins);
        
        return $service;    
    }
}
