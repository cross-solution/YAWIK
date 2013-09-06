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
        //$this->layout('layout/apply');
        $services = $this->getServiceLocator();
        
        $job = $services->get('repositories')->get('job')->findByApplyId($this->params('jobId'));
        
        $form = $this->getServiceLocator()->get('FormElementManager')->get('Application');
        $viewModel = new ViewModel();
        $viewModel->setVariables(array(
            'job' => $job,
            'form' => $form,
            'isApplicationSaved' => false,
        ));
        
        $request = $this->getRequest();
       
        if ($request->isPost()) {
            $services = $this->getServiceLocator();
            $repository = $services->get('repositories')->get('Application');
            
            
            $applicationEntity = $services->get('builders')->get('Application')->getEntity(); 
            $form->bind($applicationEntity);
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
                $applicationEntity->setStatus('new');
                $repository->save($applicationEntity);
                $applicationEntity->setId('test');
                
                if ($request->isXmlHttpRequest()) {
                    return new JsonModel(array(
                        'ok' => true,
                        'id' => $applicationEntity->id,
                        'jobId' => $applicationEntity->jobId,
                    ));
                }
                $viewModel->setVariable('isApplicationSaved', true);
            }
        } else {
            
            $form->populateValues(array(
                'jobId' => $this->params('jobId', 0),
            ));
            
        }
        return $viewModel;
        
    }
    
    
    
    
}
