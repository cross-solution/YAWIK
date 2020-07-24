<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright 2020 CROSS Solution <https://www.cross-solution.de>
 * @license MIT
 */

declare(strict_types=1);

namespace Applications\Controller;

use Applications\Entity\Contact;
use Applications\Entity\Hydrator\ApiApplicationHydrator;
use Applications\Repository\Application as ApplicationsRepository;
use Auth\Entity\AnonymousUser;
use Jobs\Repository\Job as JobsRepository;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class ApiApplyController extends AbstractActionController
{
    private $appRepository;
    private $jobRepository;
    private $formContainer;
    private $hydrator;

    public function __construct(
        ApplicationsRepository $appRepository,
        JobsRepository $jobRepository,
        $formContainer,
        ApiApplicationHydrator $hydrator
    ) {
        $this->appRepository = $appRepository;
        $this->jobRepository = $jobRepository;
        $this->formContainer = $formContainer;
        $this->hydrator = $hydrator;
    }

    public function indexAction()
    {
        /** @var \Laminas\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->noPostRequestModel();
        }

        $applyId = $this->params()->fromQuery('applyId');
        $job = $this->jobRepository->findOneBy(['applyId' => $applyId]);

        if (!$job) {
            return $this->noJobFoundModel($applyId);
        }

        $data = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );


        $application = $this->appRepository->create();
        $application->setJob($job);
        $user = $this->auth()->getUser();
        $application->setUser($user);
        $application->setContact(new Contact());

        $hydrator = $this->hydrator;
        $hydrator->hydrate($data, $application);
        //$application->getContact()->setFirstName($data['contact']['firstName']);


        $this->appRepository->store($application);

        $result = [
            'status' => 'OK',
            'id' => $application->getId(),
            'entity' => $hydrator->extract($application)
        ];

        if ($user instanceof AnonymousUser) {
            $result['track'] =
                $this->url()->fromRoute(
                    'lang/applications/detail',
                    ['id' => $application->getId()],
                    ['force_canonical' => true],
                    true
                )
                . '?token=' . $user->getToken()
            ;
        }

        $model = new JsonModel($result);
        return $model;
    }

    private function noPostRequestModel()
    {
        /** @var \Laminas\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();
        $response->setStatusCode($response::STATUS_CODE_405);

        return new JsonModel([
            'status' => 'error',
            'message' => 'Invalid request method. Only POST allowed.'
        ]);
    }

    private function invalidDataModel($e)
    {
        /** @var \Laminas\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();
        $response->setStatusCode($response::STATUS_CODE_405);

        return new JsonModel([
            'status' => 'error',
            'message' => 'Invalid json data: ' . $e->getMessage()
        ]);
    }

    private function noJobFoundModel($applyId)
    {
        /** @var \Laminas\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();
        $response->setStatusCode($response::STATUS_CODE_400);

        return new JsonModel([
            'status' => 'error',
            'message' => 'No job found with apply id "' . $applyId . '"',
        ]);
    }

    private function invalidApplicationDataModel()
    {
        /** @var \Laminas\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();
        $response->setStatusCode($response::STATUS_CODE_400);

        return new JsonModel([
            'status' => 'error',
            'message' => 'Invalid application data.',
            'errrors' => $this->formContainer->getMessages()
        ]);
    }
}
