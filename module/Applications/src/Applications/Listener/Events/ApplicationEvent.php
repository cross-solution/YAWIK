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
use Zend\EventManager\Event;
use Zend\EventManager\Exception;

/**
 * The Application event.
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @todo   write test
 */
class ApplicationEvent extends Event
{
    /**
     * Job events triggered by eventmanager
     */

    /**
     * Event is fired when a new application is saved.
     */
    const EVENT_APPLICATION_POST_CREATE   = 'application.post.create';

    /**
     * Event is fired when a users deleted application
     */
    const EVENT_APPLICATION_PRE_DELETE   = 'application.pre.delete';

    /**
     * Event is fired when an applicant is accepted
     */
    const EVENT_ACCEPT_APPLICANT   = 'application.accept.applicant';

    /**
     * Event is fired when an applicant is accepted
     */
    const EVENT_INVITE_APPLICANT   = 'application.invite.applicant';

    /**
     * Event is fired when an applicant is accepted
     */
    const EVENT_REJECT_APPLICANT   = 'application.reject.applicant';


    /**
     * @var Application $application
     */
    protected $application;

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
        } elseif (is_object($params) && isset($params->job)) {
            $this->setApplicationEntity($params->application);
        }

        return parent::setParams($params);
    }
}
