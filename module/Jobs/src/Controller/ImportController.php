<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Geo\Entity\Geometry\Point;
use Jobs\Entity\Location;
use Jobs\Entity\TemplateValues;
use Organizations\Entity\Employee;
use Psr\Container\ContainerInterface;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\JsonModel;
use Core\Entity\PermissionsInterface;
use Jobs\Listener\Events\JobEvent;
use Jobs\Listener\Response\JobResponse;

/**
 *
 *
 */
class ImportController extends AbstractActionController
{

    /**
     *
     *
     * @var ServiceManager
     */
    private $serviceLocator;
    public static function factory(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new self();
        $controller->serviceLocator = $container;

        return $controller;
    }

    /**
     * api-interface for transferring jobs
     * @return JsonModel
     */
    public function saveAction()
    {
        $services = $this->serviceLocator;

        /* @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();

        $params          = $this->params();
        $p               = $params->fromPost();
        /* @var \Auth\Entity\User $user */
        $user            = $services->get('AuthenticationService')->getUser();
        $repositories    = $services->get('repositories');
        /* @var \Jobs\Repository\Job $repositoriesJob */
        $repositoriesJob = $repositories->get('Jobs/Job');
        $log             = $services->get('Core/Log');

        $result = array('token' => session_id(), 'isSaved' => false, 'message' => '', 'portals' => array());
        try {
            if (isset($user) && !empty($user->getLogin())) {
                $formElementManager = $services->get('FormElementManager');
                /* @var \Jobs\Form\Import $form */
                $form               = $formElementManager->get('Jobs/Import');
                $id                 = $params->fromPost('id'); // determine Job from Database
                /* @var \Jobs\Entity\Job $entity */
                $entity             = null;
                $createdJob         = true;

                if (empty($id)) {
                    $applyId = $params->fromPost('applyId');
                    if (empty($applyId)) {
                        $entity = $repositoriesJob->create();
                    } else {
                        $entity = $repositoriesJob->findOneBy(array("applyId" => (string) $applyId));
                        if (!isset($entity)) {
                            // new Job (the more likely branch)
                            $entity =$repositoriesJob->create(array("applyId" => (string) $applyId));
                        } else {
                            $createdJob = false;
                        }
                    }
                } else {
                    $repositoriesJob->find($id);
                    $createdJob = false;
                }
                //$services->get('repositories')->get('Jobs/Job')->store($entity);
                $form->bind($entity);
                if ($request->isPost()) {
                    $loginSuffix                   = '';
                    $event                         = $this->getEvent();
                    $event->setName('login.getSuffix');
                    $loginSuffixResponseCollection = $this->getEventManager()->triggerEvent($event);
                    if (!$loginSuffixResponseCollection->isEmpty()) {
                        $loginSuffix = $loginSuffixResponseCollection->last();
                    }
                    $params                        = $request->getPost();
                    $params->datePublishStart      = \Datetime::createFromFormat("Y-m-d", $params->datePublishStart);
                    $result['post']                = $_POST;
                    $form->setData($params);
                    if ($form->isValid()) {
                        if (isset($params['description'])) {
                            $templateValues = new TemplateValues();
                            $description = Json::decode($params->description);
                            $entity->setTemplateValues($templateValues->setDescription(strip_tags($description)));
                        }

                        $entity->setStatus($params['status']);
                        /*
                         * Search responsible user via contactEmail
                         * @var \Auth\Repository\User $users
                         */
                        $users = $repositories->get('Auth/User');
                        $responsibleUser = $users->findByEmail($params['contactEmail']);

                        $entity->setUser($responsibleUser ?: $user);

                        $group = $user->getGroup($entity->getCompany());
                        if ($group) {
                            $entity->getPermissions()->grant($group, PermissionsInterface::PERMISSION_VIEW);
                        }
                        $result['isSaved'] = true;
                        $log->info('Jobs/manage/saveJob [user: ' . $user->getLogin() . ']:' . var_export($p, true));

                        if (!empty($params->companyId)) {
                            $companyId                = $params->companyId . $loginSuffix;
                            $repOrganization          = $repositories->get('Organizations/Organization');
                            $hydratorManager          = $services->get('HydratorManager');
                            /* @var \Organizations\Entity\Hydrator\OrganizationHydrator $hydrator */
                            $hydrator                 = $hydratorManager->get('Hydrator\Organization');
                            $entityOrganizationFromDB = $repOrganization->findbyRef($companyId);
                            //$permissions              = $entityOrganizationFromDB->getPermissions();
                            $data = array(
                                'externalId'      => $companyId,
                                'organizationName' => $params->company,
                                'image'            => $params->logoRef,
                                'user'            => $user
                            );
                            //$permissions->grant($user, PermissionsInterface::PERMISSION_CHANGE);

                            $entityOrganization = $hydrator->hydrate($data, $entityOrganizationFromDB);

                            if ($params->companyUrl) {
                                $entityOrganization->getContact()->setWebsite($params->companyUrl);
                            }

                            if ($responsibleUser && $user !== $responsibleUser) {
                                /*
                                 * We cannot use custom collections yet
                                 * @todo if we updated Mongo ODM to >1.0.5, we must move this to
                                 *       a custom collection class
                                 */
                                $employees = $entityOrganization->getEmployees();
                                $contained = false;
                                /*
                                 * this is o(n) and should propably be optimized when the custom collection is created.
                                 * It's not very performant to load the whole user entity, when all we need is the ID.
                                 * Maybe store the id as reference in the Employees Entity.
                                 */
                                foreach ($employees as $employee) {
                                    if ($employee->getUser()->getId() == $responsibleUser->getId()) {
                                        $contained = true;
                                        break;
                                    }
                                }
                                if (!$contained) {
                                    $employees->add(new Employee($responsibleUser));
                                }
                            }
                            $repositories->store($entityOrganization);
                            $entity->setOrganization($entityOrganization);
                        } else {
                            $result['message'] = '';
                        }

                        if (!empty($params->locations)) {
                            $locations = \Zend\Json\Json::decode($params->locations, \Zend\Json\Json::TYPE_ARRAY);
                            $jobLocations = $entity->getLocations();
                            $jobLocations->clear();
                            foreach ($locations as $locData) {
                                $location = new Location();
                                $coords = array_map(function ($i) {
                                    return (float) $i;
                                }, $locData['coordinates']);
                                $location->setCountry($locData['country'])
                                         ->setRegion($locData['region'])
                                         ->setCity($locData['city'])
                                         ->setCoordinates(new Point($coords));

                                $jobLocations->add($location);
                            }
                        }

                        /* @var \Core\EventManager\EventManager $jobEvents
                         * @var JobEvent $jobEvent */
                        $jobEvents = $services->get('Jobs/Events');
                        $jobEvent = $jobEvents->getEvent(JobEvent::EVENT_IMPORT_DATA, $this);
                        $jobEvent->setJobEntity($entity);

                        $extra = [];
                        foreach (array('channels', 'position', 'branches', 'keywords', 'description') as $paramName) {
                            $data = $params->get($paramName);
                            if ($data) {
                                $data = Json::decode($data, Json::TYPE_ARRAY);
                                $extra[$paramName] = $data;
                                $jobEvent->setParam($paramName, $data);
                            }
                        }

                        $jobEvents->triggerEvent($jobEvent);
                        $repositoriesJob->store($entity);

                        $id = $entity->getId();
                        if (!empty($id)) {
                            $jobEvent = $services->get('Jobs/Event'); // intentinally override
                            $jobEvent->setJobEntity($entity);

                            if (isset($extra['channels'])) {
                                foreach ($extra['channels'] as $portalName => $trash) {
                                    $jobEvent->addPortal($portalName);
                                }
                            }
                            $jobEvent->setParam('extraData', $extra);

                            if ($createdJob || true) {
                                /* @var $jobEvents \Zend\EventManager\EventManager */
                                $jobEvents = $services->get('Jobs/Events');
                                $jobEvent->setName(JobEvent::EVENT_JOB_ACCEPTED);
                                $jobEvent->setTarget($this);
                                $responses = $jobEvents->triggerEvent($jobEvent);

                                foreach ($responses as $response) {
                                    // responses from the portals
                                    // @TODO, put this in some conclusion and meaningful messages
                                    if (!empty($response)) {
                                        if ($response instanceof JobResponse) {
                                            if (!array_key_exists('log', $result)) {
                                                $result['log'] = '';
                                            }
                                            //$message = $response->getMessage();
                                            //$result['log'] .= $response->getMessage() . PHP_EOL;
                                            $status = $response->getStatus();
                                            $portal = $response->getPortal();
                                            if (empty($portal)) {
                                                throw new \RuntimeException('Publisher-Events (internal error): There is an unregistered publisher listening');
                                            }
                                            switch ($status) {
                                                case JobResponse::RESPONSE_FAIL:
                                                case JobResponse::RESPONSE_NOTIMPLEMENTED:
                                                case JobResponse::RESPONSE_ERROR:
                                                    $result['isSaved'] = false;
                                                    break;
                                                case JobResponse::RESPONSE_DENIED:
                                                case JobResponse::RESPONSE_OK:
                                                case JobResponse::RESPONSE_OKANDSTOP:
                                                case JobResponse::RESPONSE_DEPRECATED:
                                                    break;
                                            }
                                            if (array_key_exists($portal, $result['portals'])) {
                                                throw new \RuntimeException('Publisher-Events (internal error): There are two publisher registered for ' . $portal);
                                            }
                                            $result['portals'][$portal] = $status;
                                        } else {
                                            throw new \RuntimeException('Publisher-Events (internal error): Response must be from the class Jobs\Listener\Response\JobResponse');
                                        }
                                    } else {
                                        throw new \RuntimeException('Publisher-Events (internal error): Response must be set');
                                    }
                                }
                            }
                        }
                    } else {
                        $log->info('Jobs/manage/saveJob [error: ' . $form->getMessages() . ']:' . var_export($p, true));
                        $result['valid Error'] = $form->getMessages();
                    }
                }
            } else {
                $log->info('Jobs/manage/saveJob [error: session lost]:' . var_export($p, true));
                $result['message'] = 'session_id is lost';
            }
        } catch (\Exception $e) {
            $result['message'] = 'exception occured: ' . $e->getMessage();
        }
        //$services->get('Core/Log')->info('Jobs/manage/saveJob result:' . PHP_EOL . var_export($p, True));
        return new JsonModel($result);
    }
}
