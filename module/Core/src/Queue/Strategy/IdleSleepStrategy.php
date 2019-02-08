<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Strategy;

use Core\Queue\MongoQueue;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\AbstractWorkerEvent;
use SlmQueue\Worker\Event\ProcessIdleEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class IdleSleepStrategy extends AbstractStrategy
{

    public $duration = 1;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            AbstractWorkerEvent::EVENT_PROCESS_IDLE,
            [$this, 'onIdle'],
            1
        );
    }

    /**
     * @param mixed $duration
     *
     * @return self
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    
    public function onIdle(ProcessIdleEvent $event)
    {
        $queue = $event->getQueue();

        if ($queue instanceof MongoQueue) {
            sleep($this->duration);
        }
    }
}
