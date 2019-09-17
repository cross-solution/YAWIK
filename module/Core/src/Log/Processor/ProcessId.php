<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Log\Processor;

use Zend\Log\Processor\ProcessorInterface;

/**
 * Adds the process id to the event array in the key 'pid'.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ProcessId implements ProcessorInterface
{
    /**
     * Adds process id to the event array.
     *
     * @param  array $event
     *
     * @return array
     */
    public function process(array $event)
    {
        $event['pid'] = getmypid();

        return $event;
    }
}
