<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Log\Notification;

interface NotificationEntityInterface
{
    const EMERG  = 0;
    const ALERT  = 1;
    const CRIT   = 2;
    const ERR    = 3;
    const WARN   = 4;
    const NOTICE = 5;
    const INFO   = 6;
    const DEBUG  = 7;

    public function setNotification($notification);

    public function getNotification();

    public function setPriority($priority);

    public function getPriority();

    public function setTarget($target);

    public function getTarget();
}
