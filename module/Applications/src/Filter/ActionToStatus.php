<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author bleek@cross-solution.de
 * @license   MIT
 */

namespace Applications\Filter;

use Zend\Filter\FilterInterface;
use Applications\Entity\StatusInterface as Status;

/**
 * Class ActionToStatus
 *
 * @package Applications\Filter
 */
class ActionToStatus implements FilterInterface
{
    protected $actionToStatusMap = array(
        'confirm' => Status::CONFIRMED,
        'invite' => Status::INVITED,
        'reset' => Status::INCOMING,
    );

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function filter($value)
    {
        return isset($this->actionToStatusMap[$value])
            ? $this->actionToStatusMap[$value]
            : false;
    }
}
