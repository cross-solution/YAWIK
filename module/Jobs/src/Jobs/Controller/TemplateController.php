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

/**
 * Handles rendering the job in formular and in preview mode
 *
 * Class TemplateController
 * @package Jobs\Controller
 */
class TemplateController extends AbstractActionController  {

    private $jobRepository;

    public function __construct(Repository\Job $jobRepository)
    {
        $this->jobRepository = $jobRepository;
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
        $model                = new ViewModel();
        $mvcEvent             = $this->getEvent();
        $applicationViewModel = $mvcEvent->getViewModel();

        $model->setTemplate('templates/default/index');
        if ($job->status != 'active' && !$this->auth()->isLoggedIn()) {
            $this->response->setStatusCode(404);
            $model->setVariable('message','job is not available');
        }
        else {
            $model->setTemplate('templates/' . $job->template . '/index');
            $applicationViewModel->setTemplate('iframe/iFrameInjection');
        }

        $model->setVariables($this->getTemplateFields($job));
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
        $model                = new ViewModel();
        $forms                = $services->get('FormElementManager');
        /** @var \Jobs\Form\JobDescriptionTemplate $formTemplate */
        $formTemplate         = $forms->get('Jobs/Description/Template', array(
            'mode' => $job->id ? 'edit' : 'new'
        ));

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

        if (!$isAjax) {
            $basePath   = $viewHelperManager->get('basepath');
            $headScript = $viewHelperManager->get('headscript');
            $headScript->appendFile($basePath->__invoke('/Core/js/core.forms.js'));
        } else {
            return new JsonModel(array('valid' => True));
        }

        $model->setTemplate('templates/' . $job->template . '/index');
        $applicationViewModel->setTemplate('iframe/iFrameInjection');

        $model->setVariables($this->getTemplateFields($job,$formTemplate));

        return $model;
    }

    /**
     * prepares the template fields depending on the mode (preview or formular)
     *
     * @param $job
     * @param JobDescriptionTemplate|null $form
     * @return array
     */
    private function getTemplateFields($job,JobDescriptionTemplate $form=null)
    {

        $uriApply = $job->uriApply;
        if (empty($uriApply)) {
            $uriApply = $this->url()->fromRoute('lang/apply', array('applyId' => $job->applyId));
        }

        $headTitle= $job->templateValues->title;
        if (empty($job->templateValues->description) && isset($job->organization)) {
            $job->templateValues->description = $job->organization->description;
        }
        if ( is_null($form)) {
            $descriptionEditable = $job->templateValues->description;
            $benefits = $job->templateValues->benefits;
            $requirements = $job->templateValues->requirements;
            $qualifications = $job->templateValues->qualifications;
            $title = $headTitle;
        } else {

            $services = $this->getServiceLocator();
            $viewHelperManager = $services->get('ViewHelperManager');
            /* @var $viewHelperForm \Core\Form\View\Helper\FormSimple */
            $viewHelperForm = $viewHelperManager->get('formsimple');

            $formDescription = $form->get('descriptionFormDescription');
            $descriptionEditable = $viewHelperForm->render($formDescription);

            $formBenefits = $form->get('descriptionFormBenefits');
            $benefits = $viewHelperForm->render($formBenefits);

            $formRequirements = $form->get('descriptionFormRequirements');
            $requirements = $viewHelperForm->render($formRequirements);

            $formQualifications = $form->get('descriptionFormQualifications');
            $qualifications = $viewHelperForm->render($formQualifications);

            $descriptionFormTitle = $form->get('descriptionFormTitle');
            $title = $viewHelperForm->render($descriptionFormTitle);

        }

        // @see http://yawik.readthedocs.org/en/latest/modules/jobs/index.html#job-templates
        $fields= array(
            'descriptionEditable' => $descriptionEditable,
            'benefits' => $benefits,
            'requirements' => $requirements,
            'qualifications' => $qualifications,
            'title' => $title,
            'uriApply' => $uriApply,
            'headTitle' => $headTitle,
            'organizationName' => $job->organization->organizationName->name,
            'street' => $job->organization->contact->street.' '.$job->organization->contact->houseNumber,
            'postalCode' => $job->organization->contact->postalcode,
            'city' => $job->organization->contact->city,
            'uriLogo' => $this->getOrganizationLogo($job->organization),
        );

        return $fields;
    }

    /**
     * Gets the organization logo. If no logo exists, take a predefined one
     *
     * @param \Organizations\Entity\Organization $organization
     * @return String
     */
    private function getOrganizationLogo(\Organizations\Entity\Organization $organization)
    {
        if ($organization->image->uri) {
            return ($organization->image->uri);
        } else {
            /** @var \Zend\ServiceManager\ServiceManager $serviceLocator */

            $serviceLocator = $this->getServiceLocator();
            $config = $serviceLocator->get('config');
            return $config['Jobs']['default_logo'];
        }
    }
}