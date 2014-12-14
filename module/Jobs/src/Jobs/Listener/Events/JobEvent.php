<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Jobs\Listener\Events;

use Zend\EventManager\Event;

class JobEvent extends Event
{
    /**#@+
     * Job events triggered by eventmanager
     */

    /**
     * The event is fired, if a new job was created
     */
    const EVENT_JOB_NEW      = 'job.new';
    const EVENT_JOB_DELETE   = 'job.delete';
    const EVENT_JOB_ERROR    = 'job.error';

    protected $jobEntity;

    /**
     * Set application instance
     *
     * @param  ApplicationInterface $application
     * @return MvcEvent
     */
    public function setJobEntity($jobEntity)
    {
        $this->jobEntity = $jobEntity;
        return $this;
    }

    /**
     * Get application instance
     *
     * @return ApplicationInterface
     */
    public function getJobEntity()
    {
        return $this->jobEntity;
    }
}
