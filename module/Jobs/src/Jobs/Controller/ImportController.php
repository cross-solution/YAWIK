<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Jobs\Entity\Status;
use Organizations\Entity\Employee;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;
use Core\Entity\PermissionsInterface;
use Jobs\Listener\Events\JobEvent;
use Jobs\Listener\Response\JobResponse;

/**
 * 
 *
 */
class ImportController extends AbstractActionController {

    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * api-interface for transferring jobs
     * @return JsonModel
     */
    public function saveAction() {

        $services = $this->getServiceLocator();
        $config   = $services->get('Config');
        
        if (True && isset($config['debug']) && isset($config['debug']['import.job']) && $config['debug']['import.job']) {

            // Test
            $this->request->setMethod('post');
            $params = new Parameters(array(
                'applyId' => '71022',
                'company' => 'Holsten 4',
                'companyId' => '1745',
                'contactEmail' => 'gelhausen@cross-solution.de',
                'title' => 'Fuhrparkleiter/-in',
                'location' => 'Bundesland, Bayern, DE',
                'link' => 'http://anzeigen.jobsintown.de/job/1/79161.html',
                'datePublishStart' => '2013-11-15',
                'status' => 'active',
                'reference' => '2130010128',
                'atsEnabled' => '1',
                'logoRef' => 'http://anzeigen.jobsintown.de/companies/logo/image-id/3263',
                'publisher' => 'http://anzeigen.jobsintown.de/feedbackJobPublish/' . '2130010128',
                'imageUrl' => 'http://th07.deviantart.net/fs71/PRE/i/2014/230/5/8/a_battle_with_the_elements_by_lordljcornellphotos-d7vns0p.jpg',
            ));
            $this->getRequest()->setPost($params);
        }        

        $params          = $this->params();
        $p               = $params->fromPost();
        $user            = $services->get('AuthenticationService')->getUser();
        $repositories    = $services->get('repositories');
        $repositoriesJob = $repositories->get('Jobs/Job');
        $log             = $services->get('Core/Log');
        //if (isset($user)) {
        //    $services->get('Core/Log')->info('Jobs/manage/saveJob ' . $user->login);
        //}
        $result = array('token' => session_id(), 'isSaved' => False, 'message' => '', 'portals' => array());
        try {
            if (isset($user) && !empty($user->login)) {
                $formElementManager = $services->get('FormElementManager');
                $form               = $formElementManager->get('Jobs/Import');
                $id                 = $params->fromPost('id'); // determine Job from Database
                $entity             = Null;
                $createdJob         = True;

                if (empty($id)) {
                    $applyId = $params->fromPost('applyId');
                    if (empty($applyId)) {
                        // new Job (propably this branch is never used since all Jobs should have an apply-Id)
                        $entity = $repositoriesJob->create();
                    } else {
                        $entity = $repositoriesJob->findOneBy(array("applyId" => (string) $applyId));
                        if (!isset($entity)) {
                            // new Job (the more likely branch)
                            $entity =$repositoriesJob->create(array("applyId" => (string) $applyId));
                        }
                        else {
                            $createdJob = False;
                        }
                    }
                } else {
                    $repositoriesJob->find($id);
                    $createdJob = False;
                }
                //$services->get('repositories')->get('Jobs/Job')->store($entity);
                $form->bind($entity);
                if ($this->request->isPost()) {
                    $loginSuffix                   = '';
                    $event                         = $this->getEvent();
                    $loginSuffixResponseCollection = $this->getEventManager()->trigger('login.getSuffix', $event);
                    if (!$loginSuffixResponseCollection->isEmpty()) {
                        $loginSuffix = $loginSuffixResponseCollection->last();
                    }
                    $params                        = $this->getRequest()->getPost();
                    $params->datePublishStart      = \Datetime::createFromFormat("Y-m-d",$params->datePublishStart);
                    $result['post']                = $_POST;
                    $form->setData($params);
                    if ($form->isValid()) {

                        $entity->setStatus($params['status']);
                        /*
                         * Search responsible user via contactEmail
                         */
                        $users = $repositories->get('Auth/User');
                        $responsibleUser = $users->findByEmail($params['contactEmail']);

                        $entity->setUser($responsibleUser ?: $user);

                        $group = $user->getGroup($entity->getCompany());
                        if ($group) {
                            $entity->getPermissions()->grant($group, PermissionsInterface::PERMISSION_VIEW);
                        }
                        $result['isSaved'] = true;
                        $log->info('Jobs/manage/saveJob [user: ' . $user->login . ']:' . var_export($p, True));

                        if (!empty($params->companyId)) {
                            $companyId                = $params->companyId . $loginSuffix;
                            $repOrganization          = $repositories->get('Organizations/Organization');
                            $hydratorManager          = $services->get('hydratorManager');
                            $hydrator                 = $hydratorManager->get('Hydrator/Organization');
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
                            if ($user !== $responsibleUser) {
                                $entityOrganization->getEmployees()->add(new Employee($responsibleUser));
                            }
                            $repositories->store($entityOrganization);
                            $entity->setOrganization($entityOrganization);

                        }
                        else {
                            $result['message'] = '';
                        }
                        $repositoriesJob->store($entity);
                        $id = $entity->getId();
                        if (!empty($id)) {
                            $jobEvent = $services->get('Jobs/Event');
                            $jobEvent->setJobEntity($entity);
                            $jobEvent->addPortal('XING');
                            if ($createdJob || True) {
                                $responses = $this->getEventManager()->trigger(JobEvent::EVENT_JOB_ACCEPTED, $jobEvent);
                                foreach ($responses as $response) {
                                    // responses from the portals
                                    // @TODO, put this in some conclusion and meaningful messages
                                    if (!empty($response)) {
                                        if ($response instanceof JobResponse) {
                                            if (!array_key_exists('log',$result)) {
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
                                        }
                                        else {
                                            throw new \RuntimeException('Publisher-Events (internal error): Response must be from the class Jobs\Listener\Response\JobResponse');
                                        }
                                    }
                                    else {
                                        throw new \RuntimeException('Publisher-Events (internal error): Response must be set');
                                    }
                                }
                            }
                        }
                    } else {
                        $log->info('Jobs/manage/saveJob [error: ' . $form->getMessages() . ']:' . var_export($p, True));
                        $result['valid Error'] = $form->getMessages();
                    }
                }
            } else {
                $log->info('Jobs/manage/saveJob [error: session lost]:' . var_export($p, True));
                $result['message'] = 'session_id is lost';
            }
        }
        catch (\Exception $e) {
            $result['message'] = 'exception occured: ' . $e->getMessage();
        }
        //$services->get('Core/Log')->info('Jobs/manage/saveJob result:' . PHP_EOL . var_export($p, True));
        return new JsonModel($result);
    }

}

