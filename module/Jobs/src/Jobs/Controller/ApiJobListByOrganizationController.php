<?php

namespace Jobs\Controller;

use Jobs\Model\ApiJobDehydrator;
use Jobs\Repository\Job;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ApiJobListByOrganizationController extends AbstractActionController
{
    /**
     * @var Job
     */
    private $jobRepository;

    /**
     * @var ApiJobDehydrator
     */
    private $apiJobDehydrator;

    public function __construct(
        Job $jobRepository,
        ApiJobDehydrator $apiJobDehydrator
    )
    {
        $this->jobRepository = $jobRepository;
        $this->apiJobDehydrator = $apiJobDehydrator;
    }

    public function indexAction()
    {
        $organizationId = $this->params()->fromRoute('organizationId', 0);

        try {
            $jobs = $this->jobRepository->findByOrganization($organizationId);
        } catch (\Exception $e) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $jsonModel=new JsonModel();
        $jsonModel->setVariables($this->apiJobDehydrator->dehydrateList($jobs));
        $jsonModel->setJsonpCallback('yawikParseJobs');

        return $jsonModel;
    }
}