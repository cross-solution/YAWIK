<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/**
 * This Filter calculates the reciptients of the notifications about incoming applications.
 */
namespace Organizations\Filter;

use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;
use Jobs\Entity\Job;

/**
 * ${CARET}
 *
 * @author Bleek Carsten <bleek@cross-solution.de>
 * @todo write test
 */
class Recipients implements FilterInterface
{
    /*
     *
     * @param  array $value
     *
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        if (!$value instanceof Job) {
        }
        $reciptients=[];

        return $reciptients;
    }
}
