<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Queue\Job;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ExceptionJobResult extends JobResult
{
    public function __construct(\Exception $e)
    {
        parent::__construct([
            'message' => $e->getMessage(),
            'extra' => $e->getTrace(),
        ]);
    }
    
}
