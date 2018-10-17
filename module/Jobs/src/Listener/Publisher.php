<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Listener;

use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Jobs\Listener\Events\JobEvent;
use Zend\EventManager\SharedEventManagerInterface;
use Jobs\Listener\Response\JobResponse;

/**
 * Job listener for publishing job opening via REST
 *
 * @author Mathias Weitz
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author fedys
 * @author Anthonius Munthi <me@itstoni.com>
 *
 * @package Jobs\Listener
 */

class Publisher implements ListenerAggregateInterface
{
    protected $serviceManager;

    protected $name = 'feedback_publisher_email';

    /**
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param EventManagerInterface $events
     * @return $this
     */
    public function attach(EventManagerInterface $events, $priority=1)
    {
        return $this;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $events->attach('Jobs', JobEvent::EVENT_JOB_ACCEPTED, array($this, 'restPost'), 10);
        return;
    }

    /**
     * @param EventManagerInterface $events
     * @return $this
     */
    public function detach(EventManagerInterface $events)
    {
        return $this;
    }

    /**
     * @param SharedEventManagerInterface $events
     * @return $this
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        return $this;
    }

    /**
     * allows an event attachment just by class
     *
     * @param JobEvent $e
     *
     * @return JobResponse
     */
    public function restPost(JobEvent $e)
    {
        $response = new JobResponse($this->name, JobResponse::RESPONSE_NOTIMPLEMENTED);
        $serviceManager = $this->serviceManager;
        if ($serviceManager->has('Jobs/RestClient')) {
            try {
                $log = $serviceManager->get('Core/Log');
                $restClient = $serviceManager->get('Jobs/RestClient');
                $provider = $serviceManager->get('Jobs/Options/Provider');

                $entity = $e->getJobEntity();

                $render = $serviceManager->get('ViewPhpRendererStrategy')->getRenderer();
                $viewModel = $serviceManager->get('Jobs/ViewModelTemplateFilter')->getModel($entity);
                $html = $render->render($viewModel);
                $absUrlFilter = $serviceManager->get('filterManager')->get('Core/HtmlAbsPathFilter');
                $html = $absUrlFilter->filter($html);

                $host = $restClient->getHost();
                if (!isset($host)) {
                    throw new \RuntimeException('no host found for Provider');
                }
                $externalIdPublisher = null;
                $referencePublisher = null;
                $publisher = $entity->getPublisher($host);
                if (isset($publisher)) {
                    $externalIdPublisher = $publisher->externalId;
                    $referencePublisher = $publisher->reference;
                }
                if (empty($externalIdPublisher)) {
                    $externalIdPublisher = $entity->applyId;
                }
                if (empty($referencePublisher)) {
                    $referencePublisher = $entity->reference;
                }

                // all this is very alpha and will be due to several changes

                // needed by now are (naming according to the Provider):
                //   applyId           = to identify the job back in the provider
                //   company           = name of the company
                //   title             =
                //   description       =
                //   location          = zip and town-name
                //   datePublishStart  = in a comprehensibly format for \DateTime
                //   channels          = array of externalIds
                $data = array(
                    'referenceId'      => $externalIdPublisher,
                    // applyId is historical, it should be replaced by referenceId
                    'applyId'          => $externalIdPublisher,
                    'reference'        => $referencePublisher,
                    'company'          => $entity->organization->name,
                    'title'            => $entity->title,
                    'description'      => $html,
                    'location'         => $entity->location,
                    'datePublishStart' => $entity->datePublishStart,
                    'channels'         => array(),
                    'templateName'     => $entity->template,
                    'contactEmail'     => $entity->contactEmail

                );
                //$hydrator = $serviceManager->get('Jobs/JobsEntityHydrator');
                //$data = $hydrator->extract($entity);

                foreach ($entity->portals as $portalName => $portal) {
                    if (array_key_exists($portal, $provider->channels)) {
                        $data['channels'][] = $provider->channels[$portal]->externalkey;
                    }
                }

                $dataJson = json_encode($data);
                $restClient->setRawBody($dataJson);
                $response = $restClient->send();
                $StatusCode = $response->getStatusCode();
                $body = $response->getBody();
                $decodedBody = json_decode($body);
                $jsonLastError = json_last_error();
                if (json_last_error() != JSON_ERROR_NONE) {
                    // not able to decode json
                    $log->info('RestCall Response not Json [errorCode: ' . $jsonLastError . ']: ' . var_export($body, true));
                } else {
                    // does the provider want to have an own ID for Identification ?
                    $response_referenceUpdate = $decodedBody->referenceUpdate;
                    $response_externalIdUpdate = $decodedBody->applyIdUpdate;

                    if ($publisher->externalId != $response_externalIdUpdate || $publisher->reference != $response_referenceUpdate) {
                        $logInfo = 'RestCall changed externalID ['
                                   . var_export($publisher->externalId, true)
                                   . ' => '
                                   . var_export($response_externalIdUpdate, true) . '], reference  ['
                                   . var_export($publisher->reference, true)
                                   . ' => '
                                   . var_export($response_referenceUpdate, true) . ']';
                        $log->info($logInfo);
                        $publisher->reference = $response_referenceUpdate;
                        $publisher->externalId = $response_externalIdUpdate;
                        $serviceManager->get('repositories')->store($entity);
                    }
                }

                $e->stopPropagation(true);
                $response = new JobResponse($this->name, JobResponse::RESPONSE_OKANDSTOP);
            } catch (\Exception $e) {
                $response = new JobResponse($this->name, JobResponse::RESPONSE_FAIL);
            }
        }
        return $response;
    }
    
    /**
     * @param ServiceManager $serviceLocator
     * @return Publisher
     */
    public static function factory(ServiceManager $serviceLocator)
    {
        return new static($serviceLocator);
    }
}
