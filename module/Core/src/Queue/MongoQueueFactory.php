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
 * @todo write test  
 */
class MongoQueueFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
        $dm = $container->get('Core/DocumentManager');
        $client = $dm->getConnection()->getMongoClient();
        if (7 == PHP_MAJOR_VERSION) {
            $client = $client->getClient();
        }
        $db = $dm->getConfiguration()->getDefaultDB();
        $name = 'core.queue';
        $collection = $client->selectCollection($db, $name);
        $jobPlugins = $container->get(JobPluginManager::class);

        $service = new MongoQueue($collection, $requestedName, $jobPlugins);
        
        return $service;    
    }
}
