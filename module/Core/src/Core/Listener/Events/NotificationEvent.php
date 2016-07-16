<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Listener\Events;

use Zend\EventManager\Event;

/**
 * has two purposes
 * #1 adding messages to the listener
 * #2 call for handling all the messages
 *
 * while adding messages is triggered by someone else
 * the call for handling all messages is triggered by the listener itself and is send to all handlers
 *
 * Class NotificationEvent
 * @package Core\Listener\Events
 */
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

    /**
     * @param $message
     *
     * @return $this
     */
    public function setNotification($message)
    {
        $this->notification = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param $messages
     *
     * @return $this
     */
    public function setNotifications($messages)
    {
        $this->notifications = $messages;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}
