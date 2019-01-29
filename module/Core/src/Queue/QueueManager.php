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

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class QueueManager 
{
    /**
     *
     *
     * @var \MongoDB\Client
     */
    private $mongoClient;
    private $config;
    private $queues = [];

    public function __construct($mongoClient, array $config = [])
    {
        $this->mongoClient = $mongoClient;
        $this->config = $config;
    }

    /**
     *
     *
     * @param $name
     *
     * @return Queue
     */
    public function get($name)
    {
        if (isset($this->queues[$name])) {
            return $this->queues[$name];
        }

        $config = $this->getConfig($name);
        $queue = new Queue($this->mongoClient->selectCollection($config['database'], $name));
        $this->queues[$name] = $queue;

        return $queue;
    }

    private function getConfig($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        return [];
    }
}
