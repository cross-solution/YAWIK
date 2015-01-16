<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace Jobs\Controller;

use Jobs\Form\Job;
use Jobs\Form\JobDescriptionTemplate;
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

    /**
     * Handels the job opening template in preview mode
     *
     * @return ViewModel
     * @throws \RuntimeException
     */
    public function viewAction()
    {
        $id = $this->params()->fromQuery('id');
        if (!$id) {
            throw new \RuntimeException('Missing job id.', 404);
        }
        $job  = $this->getServiceLocator()->get('repositories')->get('Jobs/Job')->find($id);
        if (!$job) {
            throw new \RuntimeException('Job not found.', 404);
        }
        $model                = new ViewModel();
        $mvcEvent             = $this->getEvent();
        $applicationViewModel = $mvcEvent->getViewModel();

        $model->setTemplate('templates/default/index');
        if ($job->status != 'active' && !$this->auth()->isLoggedIn()) {
            $this->response->setStatusCode(404);
            $model->setVariable('message','job is not available');
        }
        else {
            $model->setTemplate('templates/default/index');
            $applicationViewModel->setTemplate('iframe/iFrameInjection');
        }

        $model->setVariables($this->getTemplateFields($job));
        return $model;
    }

    /**
     * Handels the job opening template in formular mode
     *
     * @return ViewModel
     */
    protected function editTemplateAction()
    {
        $request              = $this->getRequest();
        $isAjax               = $request->isXmlHttpRequest();
        $params               = $this->params();
        $formIdentifier       = $params->fromQuery('form');
        $services             = $this->getServiceLocator();
        $viewHelperManager    = $services->get('ViewHelperManager');
        $mvcEvent             = $this->getEvent();
        $id                   = $this->params('id');
        $applicationViewModel = $mvcEvent->getViewModel();
        $repositories         = $services->get('repositories');
        $repositoryJob        = $repositories->get('Jobs/Job');
        $job                  = $repositoryJob->find($id);
        $model                = new ViewModel();
        $forms                = $services->get('FormElementManager');
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
            //$headScript->appendScript('$(document).ready(function() { $() });');
        } else {
            return new JsonModel(array('valid' => True));
        }

        $model->setTemplate('templates/default/index');
        $applicationViewModel->setTemplate('iframe/iFrameInjection');

        $model->setVariables($this->getTemplateFields($job,$formTemplate));

        return $model;
    }

    /**
     * prepares the template fields depending on the mode
     *
     * @param $job
     * @param JobDescriptionTemplate|null $form
     * @return array
     */
    protected function getTemplateFields($job,JobDescriptionTemplate $form=null)
    {

        $uriApply = $job->uriApply;
        if (empty($uriApply)) {
            $uriApply = $this->url()->fromRoute('lang/apply', array('applyId' => $job->applyId));
        }

        if ( is_null($form)){
            $benefits = $job->templateValues->benefits;
            $requirements = $job->templateValues->requirements;
            $qualifications = $job->templateValues->qualifications;
            $title = $job->templateValues->title;
        } else {

            $services = $this->getServiceLocator();
            $viewHelperManager = $services->get('ViewHelperManager');
            /* @var $viewHelperForm \Core\Form\View\Helper\FormSimple */
            $viewHelperForm = $viewHelperManager->get('formsimple');

            $formBenefits = $form->get('descriptionFormBenefits');
            $benefits = $viewHelperForm->render($formBenefits);

            $formRequirements = $form->get('descriptionFormRequirements');
            $requirements = $viewHelperForm->render($formRequirements);

            $formQualifications = $form->get('descriptionFormQualifications');
            $qualifications = $viewHelperForm->render($formQualifications);

            $descriptionFormTitle = $form->get('descriptionFormTitle');
            $title = $viewHelperForm->render($descriptionFormTitle);
        }

        $fields= array(
            'benefits' => $benefits,
            'requirements' => $requirements,
            'qualifications' => $qualifications,
            'title' => $title,
            'uriApply' => $uriApply,
            'organizationName' => $job->organization->organizationName->name,
            'street' => $job->organization->contact->street.' '.$job->organization->contact->houseNumber,
            'postalCode' => $job->organization->contact->postalcode,
            'city' => $job->organization->contact->city,
            'uriLogo' => $job->organization->image->uri, // @todo: set a default logo, if no logo is available
            'description' => $job->organization->description,
        );

        return $fields;
    }
}