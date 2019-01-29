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
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class ProcessId implements ProcessorInterface
{

    /**
     * Processes a log message before it is given to the writers
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
