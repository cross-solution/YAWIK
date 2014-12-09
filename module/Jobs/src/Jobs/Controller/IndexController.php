<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Jobs */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;
use Zend\View\Model\JsonModel;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class IndexController extends AbstractActionController
{
    /**
     * attaches further Listeners for generating / processing the output
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $jobServices     = $serviceLocator->get('Jobs/Listeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        $events->attach($jobServices);
        return $this;
    }

    /**
     * List jobs
     */
    public function indexAction()
    { 
        
        $params      = $this->getRequest()->getQuery();
        $jsonFormat  = 'json' == $params->get('format');
        $repository  = $this->getServiceLocator()->get('repositories')->get('Jobs/Job');
        $isRecruiter = $this->acl()->isRole('recruiter');
        
        if (!$jsonFormat && !$this->getRequest()->isXmlHttpRequest()) {
            $session = new Session('Jobs\Index');
            $sessionKey = $this->auth()->isLoggedIn() ? 'userParams' : 'guestParams';
            $sessionParams = $session[$sessionKey];
            if ($sessionParams) {
                foreach ($sessionParams as $key => $value) {
                    $params->set($key, $params->get($key, $value));
                }
            } else if ($isRecruiter) {
                $params->set('by', 'me');
            }
            $session[$sessionKey] = $params->toArray();
            $filterForm = $this->getServiceLocator()->get('forms')->get('Jobs/ListFilter', $isRecruiter);
            $filterForm->bind($params);
            //$filterForm->setData(array('params' => $params->toArray()));
            //$filterForm->setData()
        }
        
        $repository = $this->getServiceLocator()->get('repositories')->get('Jobs/Job');
        
        if (!isset($params['sort'])) {
            $params['sort']='-date';
        }
        
        $paginator = $this->paginator('Jobs/Job',$params);
        
//         $jsonFormat = 'json' == $this->params()->fromQuery('format');
        
//         if ($jsonFormat) {
//             $viewModel = new JsonModel();
//             //$items = iterator_to_array($paginator);
            
//             $viewModel->setVariables(array(
//                 'items' => $this->getServiceLocator()->get('builders')->get('JsonApplication')
//                                 ->unbuildCollection($paginator->getCurrentItems()),
//                 'count' => $paginator->getTotalItemCount()
//             ));
//             return $viewModel;
            
//         } 
        
        $return = array(
            'by' => $params->get('by', 'all'),
            'jobs' => $paginator,
        );
        if (isset($filterForm)) {
            $return['filterForm'] = $filterForm;
        }
        return $return;
        
    
     }
     
     public function viewAction()
     {
         $id = $this->params()->fromQuery('id');
         if (!$id) {
             throw new \RuntimeException('Missing job id.', 404);
         }
         $job = $this->getServiceLocator()->get('repositories')->get('Jobs/Job')->find($id);
         if (!$job) {
             throw new \RuntimeException('Job not found.', 404);
         }
         $model                = new ViewModel();
         $mvcEvent             = $this->getEvent();
         $applicationViewModel = $mvcEvent->getViewModel();

         $templateValues = $job->templateValues;

         $model->setTemplate('templates/default/index.phtml');
         $applicationViewModel->setTemplate('iframe/iFrameInjection');

         $uriApply = $job->uriApply;
         if (empty($uriApply)) {
             $uriApply = $this->url()->fromRoute('lang/apply', array('applyId' => $job->applyId));
         }

         $model->setVariables(array(
             'job' => $job,
             'benefits' => $templateValues->benefits,
             'requirements' => $templateValues->requirements,
             'qualifications' => $templateValues->qualifications,
             'title' => $templateValues->title,
             'uriApply' => $uriApply
         ));
         return $model;
         
     }
     
     public function dashboardAction()
     {
         $services = $this->getServiceLocator();
         $params = $this->getRequest()->getQuery();
         $isRecruiter = $this->acl()->isRole('recruiter');
         if ($isRecruiter) {
             $params->set('by', 'me');
         }
         $myJobs = $services->get('repositories')->get('Jobs/Job');
         
         $paginator = $this->paginator('Jobs/Job');

         #$paginator->setCurrentPageNumber($this->params()->fromQuery('page', 1))
         #->setItemCountPerPage($params->get('count', 10));
         
         return array(
             'script' => 'jobs/index/dashboard',
             'type' => $this->params('type'),
             'myJobs' => $myJobs,
             'jobs' => $paginator
         );
     }
     
     public function typeaheadAction()
     {
         $query = $this->params()->fromQuery('q', '*');
         $repository = $this->getServiceLocator()
                            ->get('repositories')
                            ->get('Jobs/Job');

         $return = array();
         foreach ($repository->getTypeaheadResults($query,$this->auth('id')) as $id => $item) {
             $return[] = array(
                 'id' => $id,
                 'title' => $item['title'],
                 'applyId' => $item['applyId'],
             );
         }
         
         return new JsonModel($return);
     }

    /**
     * Handles the privacy policy used in an application form.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function termsAction()
    {
        $viewModel = new ViewModel();
        if ($this->request->isXmlHttpRequest()) {
            $viewModel->setTerminal(true);
        }
        return $viewModel;
    }
    
}
