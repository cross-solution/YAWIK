<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Listener\Events;

use Zend\EventManager\Event;

class NotificationEvent extends Event
{
    const EVENT_NOTIFICATION_ADD   = 'notification.add';
    const EVENT_NOTIFICATION_CLEAR = 'notification.clear';
    const EVENT_NOTIFICATION_FETCH = 'notification.fetch';
    const EVENT_NOTIFICATION_HTML  = 'notification.html';

    const NOTIFICATION_SEVERITY_ERROR   = 'error';
    const NOTIFICATION_SEVERITY_WARNING = 'warning';

    protected $notification;
    protected $notifications;

    public function setNotification($message) {
        $this->notification = $message;
        return $this;
    }

    public function getNotification() {
        return $this->notification;
    }

    public function setNotifications($messages) {
        $this->notifications = $messages;
        return $this;
    }

    public function getNotifications() {
        return $this->notifications;
    }

}
