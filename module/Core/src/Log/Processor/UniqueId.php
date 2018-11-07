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

use Zend\Log\Processor\RequestId;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class UniqueId extends RequestId
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
        $event = parent::process($event);

        $event['uniqueId'] = substr($event['extra']['requestId'], 0, 7);
        unset($event['extra']['requestId']);

        return $event;
    }
}
