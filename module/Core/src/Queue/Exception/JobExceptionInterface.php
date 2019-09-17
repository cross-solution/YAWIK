<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Exception;

/**
 * Interface for all JobExceptions.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface JobExceptionInterface 
{
    /**
     * Set options to be passed on to the queue upon reinserting or failing the job.
     *
     * @param array $options
     */
    public function setOptions(array $options) : void;

    /**
     * Get the options for the queue.
     *
     * @return array
     */
    public function getOptions() : array;
}
