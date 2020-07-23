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
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Applications\Repository\Application as ApplicationsRepository;
use Jobs\Repository\Job as JobsRepository;

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

    public function __construct(
        ApplicationsRepository $appRepository,
        JobsRepository $jobRepository,
        $formContainer
    ) {
        $this->appRepository = $appRepository;
        $this->jobRepository = $jobRepository;
        $this->formContainer = $formContainer;
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

        $hydrator = new ApiApplicationHydrator();
        $hydrator->hydrate($data, $application);
        //$application->getContact()->setFirstName($data['contact']['firstName']);


        $result = [
            'status' => 'OK',
            'id' => $application->getId(),
            'test' => $application->getContact()->getFirstName(),
            'test2' => $application->getJob()->getId(),
            'entity' => $hydrator->extract($application)
        ];

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
