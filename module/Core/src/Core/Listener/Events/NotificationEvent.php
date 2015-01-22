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

    protected $message;

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

}
