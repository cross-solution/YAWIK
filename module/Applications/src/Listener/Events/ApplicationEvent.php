<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace  Applications\Listener\Events;

use Applications\Entity\Application;
use Auth\Entity\User;
use Zend\EventManager\Event;
use Zend\EventManager\Exception;

/**
 * The Application event.
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
class ApplicationEvent extends Event
{

    /**
     * Event is fired when a new application is saved.
     */
    const EVENT_APPLICATION_POST_CREATE   = 'application.post.create';

    /**
     * Event is fired when a users deleted application
     */
    const EVENT_APPLICATION_PRE_DELETE   = 'application.pre.delete';

    /**
     * Event is fired when the status of an application is changed
     */
    const EVENT_APPLICATION_STATUS_CHANGE   = 'application.status.change';

    /**
     * @var Application $application
     */
    protected $application;

    /**
     * @var array POST Data
     */
    protected $formData;

    /**
     * @var
     */
    protected $notification;

    /**
     * @var
     */
    protected $user;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @var bool isPostRequest
     */
    protected $isPostRequest;

    /**
     * @var array $postData
     */
    protected $postData;


    /**
     * Sets the application entity
     *
     * @param  Application $application
     * @return $this
     */
    public function setApplicationEntity(Application $application)
    {
        $this->application = $application;
        return $this;
    }


    /**
     * Gets the application entity
     *
     * @return Application
     */
    public function getApplicationEntity()
    {
        return $this->application;
    }

    /**
     * @param $array
     *
     * @return $this
     */
    public function setFormData($array)
    {
        $this->formData = $array;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $notification
     *
     * @return $this
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
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
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function isPostRequest()
    {
        return $this->isPostRequest;
    }

    /**
     * @param $flag
     *
     * @return $this
     */
    public function setIsPostRequest($flag)
    {
        $this->isPostRequest = $flag;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostData()
    {
        return $this->postData;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function setPostData($data)
    {
        $this->postData = $data;
        return $this;
    }


    /**
     * Sets parameters.
     *
     * @internal Sets the application entity when passed in $params under the key/property named "application".
     *           This is because in the ApplicationEventManager, the default event class is set to this,
     *
     * @since 0.25
     */
    public function setParams($params)
    {
        if (is_array($params) && isset($params['application'])) {
            $this->setApplicationEntity($params['application']);
            unset($params['application']);
        } elseif (is_object($params) && isset($params->application)) {
            $this->setApplicationEntity($params->application);
        }

        if (is_array($params) && isset($params['user'])) {
            $this->setUser($params['user']);
            unset($params['user']);
        }

        if (is_array($params) && isset($params['status'])) {
            $this->setStatus($params['status']);
            unset($params['status']);
        }

        return parent::setParams($params);
    }
}
