<?php

namespace Jobs\Controller;

use Jobs\Entity\StatusInterface;

use Jobs\Model\ApiJobDehydrator;
use Jobs\Repository;
use Zend\Http\PhpEnvironment\Response;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ApiJobListByOrganizationController extends AbstractActionController
{
    /**
     * @var Repository\Job
     */
    private $jobRepository;

    /**
     * @var ApiJobDehydrator
     */
    private $apiJobDehydrator;

    /**
     * @var InputFilter
     */
    private $filter;


    public function __construct(
        Repository\Job $jobRepository,
        ApiJobDehydrator $apiJobDehydrator,
        InputFilter $filter
    ) {
        $this->jobRepository = $jobRepository;
        $this->apiJobDehydrator = $apiJobDehydrator;
        $this->filter = $filter;
    }

    public function indexAction()
    {
        $organizationId = $this->params()->fromRoute('organizationId', 0);
        $callback = $this->filter->setData($_GET)->getValue('callback');

        $status = $this->filter->getValue('status');
        if (true === $status) { $status = null; }
        elseif (!$status) { $status = StatusInterface::ACTIVE; }

        try {
            $jobs = $this->jobRepository->findByOrganization($organizationId, $status);
        } catch (\Exception $e) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_404);
            return $response;
        }
        $jsonModel=new JsonModel();
        $jsonModel->setVariables($this->apiJobDehydrator->dehydrateList($jobs));
        $jsonModel->setJsonpCallback($callback);

        return $jsonModel;
    }
}
