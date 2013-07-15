<?php
/**
 * Cross Applicant Management
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Applications controller */
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Applications\Form\Application as ApplicationForm;
use Applications\Model\Application as ApplicationModel;
use Applications\Form\ApplicationHydrator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\JsonModel;

/**
 * Main Action Controller for Applications module.
 *
 */
class IndexController extends AbstractActionController
{
    
    /**
     * Main apply site
     *
     */
    public function indexAction()
    { 
//         $view = new ViewModel();
//         $view->setTerminal(true);
//         return $view;
        $this->layout('layout/apply');
       
        $form = $this->getServiceLocator()->get('FormElementManager')->get('Application');
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'job' => (object) array(
                'title' => 'Testjob'
            ),
            'form' => $form,
            'isApplicationSaved' => false,
        ));
        
        $request = $this->getRequest();
       
        if ($request->isPost()) {
            $repository = $this->getServiceLocator()->get('ApplicationRepository');
            
            $applicationModel = $repository->getApplicationBuilder()->build(); 
            $form->bind($applicationModel);
            $data = $this->params()->fromPost();
            $form->setData($data);
            if (!$form->isValid()) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonModel(array(
                        'ok' => false,
                        'messages' => $form->getMessages()
                    ));
                }
                //$form->populateValues($data);
            } else {
                $repository->save($applicationModel);
                
                if ($request->isXmlHttpRequest()) {
                    return new JsonModel(array(
                        'ok' => true,
                    ));
                }
                $viewModel->setVariable('isApplicationSaved', true);
            }
        } else {
            $form->populateValues(array(
                'application' => array('jobid' => $this->params('jobid', 0)),
            ));
            
        }
        return $viewModel;
        
    }
    
    public function submitAction()
    {
        $model = new ApplicationModel();
        $form = new ApplicationForm($model);
        
        $form->setHydrator(new ClassMethods());
        $form->bind($model);
        
        $form->setData($this->params()->fromPost());
        
        $form->isValid();
        
        var_dump($this->params()->fromPost(), $model, $form->getData());
    }
    
    
}
