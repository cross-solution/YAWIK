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
    /**
     * Job events triggered by eventmanager
     */

    /**
     * a new job was created
     */
    const EVENT_NEW            = 'job.new';
    /**
     * portals have changed
     */
    const EVENT_SEND_PORTALS   = 'job.portals';

    protected $jobEntity;

    /**
     * Set jobentity
     *
     * @param  Jobs\Entity $jobEntity
     * @return MvcEvent
     */
    public function setJobEntity($jobEntity)
    {
        $this->jobEntity = $jobEntity;
        return $this;
    }

    /**
     * Get jobentity
     *
     * @return Jobs\Entity
     */
    public function getJobEntity()
    {
        return $this->jobEntity;
    }
}
