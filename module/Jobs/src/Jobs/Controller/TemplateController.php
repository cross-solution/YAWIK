<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author bleek@cross-solution.de
 * @license   MIT
 */

namespace Jobs\Controller;

use Jobs\Form\JobDescriptionTemplate;
use Jobs\Repository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\AbstractOptions;

/**
 * Handles rendering the job in formular and in preview mode
 *
 * Class TemplateController
 * @package Jobs\Controller
 */
class TemplateController extends AbstractActionController
{

    /**
     * @var Repository\Job $jobRepository
     */
    private $jobRepository;

    /**
     * @var AbstractOptions
     */
    protected $config;

    public function __construct(Repository\Job $jobRepository, AbstractOptions $config)
    {
        $this->jobRepository = $jobRepository;
        $this->config = $config;
    }

    /**
     * Handles the job opening template in preview mode
     *
     * @return ViewModel
     * @throws \RuntimeException
     */
    public function viewAction()
    {
        $id = $this->params()->fromQuery('id');
        $job = $this->jobRepository->find($id);
        $services             = $this->getServiceLocator();
        $mvcEvent             = $this->getEvent();
        $applicationViewModel = $mvcEvent->getViewModel();

        $isAdmin=$this->auth()->isAdmin();

        $model = $services->get('Jobs/viewModelTemplateFilter')->__invoke($job);

        # @todo make this working for anonymous users
//        if ($job->status != 'active' && !$job->getPermissions()->isChangeGranted($this->auth()->getUser()) && ! $isAdmin) {
//            $this->response->setStatusCode(404);
//            $model->setVariable('message','job is not available');
//        } else {
            $applicationViewModel->setTemplate('iframe/iFrameInjection');
//        }
        return $model;
    }

    /**
     * Handles the job opening template in formular mode
     *
     * @return ViewModel
     */
    protected function editTemplateAction()
    {
        $id = $this->params('id');
        $formIdentifier=$this->params()->fromQuery('form');
        $job = $this->jobRepository->find($id);

        $request              = $this->getRequest();
        $isAjax               = $request->isXmlHttpRequest();
        $services             = $this->getServiceLocator();
        $viewHelperManager    = $services->get('ViewHelperManager');
        $mvcEvent             = $this->getEvent();
        $applicationViewModel = $mvcEvent->getViewModel();
        $forms                = $services->get('FormElementManager');
        /** @var \Jobs\Form\JobDescriptionTemplate $formTemplate */
        $formTemplate         = $forms->get(
            'Jobs/Description/Template',
            array(
            'mode' => $job->id ? 'edit' : 'new'
            )
        );

        $formTemplate->setParam('id', $job->id);
        $formTemplate->setParam('applyId', $job->applyId);
        $formTemplate->setEntity($job);

        if (isset($formIdentifier) && $request->isPost()) {
            // at this point the form get instanciated and immediately accumulated
            $instanceForm = $formTemplate->get($formIdentifier);
            if (!isset($instanceForm)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            // the id is part of the postData, but it never should be altered
            $postData = $request->getPost();
            unset($postData['id']);
            unset($postData['applyId']);
            $instanceForm->setData($postData);
            if ($instanceForm->isValid()) {
                $this->getServiceLocator()->get('repositories')->persist($job);
            }
        }

        $model = $services->get('Jobs/ViewModelTemplateFilter')->__invoke($formTemplate);

        if (!$isAjax) {
            $basePath   = $viewHelperManager->get('basepath');
            $headScript = $viewHelperManager->get('headscript');
            $headScript->appendFile($basePath->__invoke('/Core/js/core.forms.js'));
        } else {
            return new JsonModel(array('valid' => true));
        }
        $applicationViewModel->setTemplate('iframe/iFrameInjection');
        return $model;
    }

    /**
     * Gets the organization logo. If no logo exists, take a predefined one
     *
     * @param \Organizations\Entity\Organization $organization
     * @return String
     */
    private function getOrganizationLogo(\Organizations\Entity\Organization $organization)
    {
        if (isset($organization) && isset($organization->image) && $organization->image->uri) {
            return ($organization->image->uri);
        } else {
            /** @var \Zend\ServiceManager\ServiceManager $serviceLocator */
            return $this->config->default_logo;
        }
    }
}
