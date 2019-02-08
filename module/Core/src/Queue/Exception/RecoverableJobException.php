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
 * Exception thrown if a job encounters a recoverable error and wants
 * to be reinserted in the queue.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class RecoverableJobException extends AbstractJobException
{

}
