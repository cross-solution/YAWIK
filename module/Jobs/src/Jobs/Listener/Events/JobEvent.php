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

use Jobs\Entity\Job;
use Zend\EventManager\Event;

class JobEvent extends Event
{
    /**
     * Job events triggered by eventmanager
     */

    /**
     * Event is fired when a users has created a new job opening and accepted the
     * terms and conditions
     */
    const EVENT_JOB_CREATED   = 'job.created';

    /**
     * Event is fired when the owner of the YAWIK installation has accepted the job
     * opening
     */
    const EVENT_JOB_ACCEPTED   = 'job.accepted';

    /**
     * Event is fired, when the owner of the YAWIK installation has rejected the job
     * opening
     */
    const EVENT_JOB_REJECTED   = 'job.rejected';


    protected $jobEntity;

    /**
     * Sets the job entity
     *
     * @param  Job $jobEntity
     * @return MvcEvent
     */
    public function setJobEntity($jobEntity)
    {
        $this->jobEntity = $jobEntity;
        return $this;
    }

    /**
     * Gets the job entity
     *
     * @return Job
     */
    public function getJobEntity()
    {
        return $this->jobEntity;
    }
}
