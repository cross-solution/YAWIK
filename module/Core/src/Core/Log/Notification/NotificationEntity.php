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

class NotificationEntity implements NotificationEntityInterface
{
    protected $notification;
    protected $priority;

    public function setNotification($notification)
    {
        $this->notification = $notification;
        return $this;
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }
}
