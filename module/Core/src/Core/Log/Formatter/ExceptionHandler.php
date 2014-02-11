<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ExceptionHandler.php */ 
namespace Core\Log\Formatter;

use Zend\Log\Formatter\ExceptionHandler as ZendExceptionHandler;
use DateTime;

class ExceptionHandler extends ZendExceptionHandler
{
    /**
     * This method formats the event for the PHP Exception
     *
     * @param array $event
     * @return string
     */
    public function format($event)
    {
        if (isset($event['timestamp']) && $event['timestamp'] instanceof DateTime) {
            $event['timestamp'] = $event['timestamp']->format($this->getDateTimeFormat());
        }
    
        $output = $event['timestamp'] . ' ' . $event['priorityName'] . ' ('
            . $event['priority'] . ') ' . $event['message'] .' in '
                . $event['extra']['file'] . ' on line ' . $event['extra']['line'];
    
        if (!empty($event['extra']['trace'])) {
            $outputTrace = '';
            foreach ($event['extra']['trace'] as $trace) {
                foreach ($trace as $key => $val) {
                    if ('args' == $key) {
                        continue;
                    }
                    if ('type' == $key) {
                        $val = $this->getType($val);
                    }
                    $outputTrace .= sprintf(
                        "    %8s: %s\n",
                        ucfirst($key), $val
                    );
                }
                $outputTrace .= str_repeat('-', 30) . PHP_EOL;
                
            }
                        $output .= "\n[Trace]\n" . $outputTrace;
        }
    
        return $output;
    }
    
}

