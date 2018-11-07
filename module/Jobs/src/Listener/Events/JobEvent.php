<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Jobs\Listener\Events;

use ArrayAccess;
use Jobs\Entity\Job;
use Zend\EventManager\Event;

/**
 * The Job event.
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
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

    /** Event is fired, when the status of an job has changed. */
    const EVENT_STATUS_CHANGED = 'job.status-changed';

    const EVENT_IMPORT_DATA    = 'job.import-data';

    /**
     * get all available names for publishing
     */
    const PORTAL_AVAIL_NAME    = 'portal.availname';

    /**
     * portals to be published
     * @var array
     */
    protected $portals = array();

    protected $jobEntity;

    /**
     * Sets the job entity
     *
     * @param  Job $jobEntity
     * @return self
     */
    public function setJobEntity(Job $jobEntity)
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

    /**
     * Sets parameters.
     *
     * @internal Sets the job entity when passed in $params under the key/property named "job".
     *           This is because in the JobEventManager, the default event class is set to this,
     *
     * @since 0.19
     */
    public function setParams($params)
    {
        if (is_array($params) && isset($params['job'])) {
            $this->setJobEntity($params['job']);
            unset($params['job']);
        } elseif (is_object($params) && isset($params->job)) {
            $this->setJobEntity($params->job);
        }

        return parent::setParams($params);
    }


    /**
     * @param $portal
     * @return $this
     */
    public function addPortal($portal)
    {
        $portal = strtolower($portal);
        if (!in_array($portal, $this->portals)) {
            $this->portals[] = $portal;
        }
        return $this;
    }

    /**
     * publisher can apply an request for publishing
     * this by no means imply they are not free to choose other means of qualification to publish a job
     * @param $portal
     * @return bool
     */
    public function hasPortal($portal)
    {
        $portal = strtolower($portal);
        return in_array($portal, $this->portals);
    }
}
