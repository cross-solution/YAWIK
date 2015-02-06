<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Listener;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Jobs\Listener\Events\JobEvent;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Stdlib\Hydrator\Filter\MethodMatchFilter;

/**
 * Job listener for triggering actions like sending mail notification
 *
 * @package CamMediaintown\Listener
 */

class Publisher implements ListenerAggregateInterface, SharedListenerAggregateInterface, ServiceManagerAwareInterface
{
    protected $serviceManager;

    public function setServiceManager(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        return $this;
    }

    public function getServiceManager() {
        return $this->serviceManager;
    }

    public function attach(EventManagerInterface $events)
    {
        //$events->attach(JobEvent::EVENT_NEW, array($this, 'jobNewMail'), 1);
        return $this;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach('Jobs', JobEvent::EVENT_JOB_ACCEPTED, array($this, 'restPost'), 10);
        return;
    }


    public function detach(EventManagerInterface $events)
    {
        return $this;
    }

    public function detachShared(SharedEventManagerInterface $events) {
        return $this;
    }


    /**
     * allows an event attachment just by class
     * @param JobEvent $e
     */
    public function restPost(JobEvent $e)
    {
        $serviceManager = $this->getServiceManager();
        if ($serviceManager->has('Jobs/RestClient')) {
            try {
                $restClient = $serviceManager->get('Jobs/RestClient');

                $entity = $e->getJobEntity();
                $hydrator = $serviceManager->get('Jobs/JsonJobsEntityHydrator');
                $json = $hydrator->extract($entity);
                $restClient->setRawBody($json);
                $response = $restClient->send();
                // @TODO: statusCode is not stored, there is simply no mechanism to track external communication.
                $StatusCode = $response->getStatusCode();

                $e->stopPropagation(true);
            }
            catch (\Exception $e) {
            }
        }
        return;

    }

}
