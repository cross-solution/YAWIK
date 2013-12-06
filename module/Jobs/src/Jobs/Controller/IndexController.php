<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Jobs */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class IndexController extends AbstractActionController
{
    
    
    /**
     * List applications
     *
     */
    public function indexAction()
    { 
        
        $params = $this->getRequest()->getQuery();
        $jsonFormat = 'json' == $params->get('format');
        $hasJobs = (bool) $this->getServiceLocator()
                               ->get('repositories')
                               ->get('job')
                               ->countByUser($this->auth('id'));
        
        if (!$jsonFormat) {
            $session = new Session('Jobs\Index');
            if ($session->params) {
                foreach ($session->params as $key => $value) {
                    $params->set($key, $params->get($key, $value));
                }
            } else if ($hasJobs) {
                $params->set('by', 'me');
            }
            $session->params = $params->toArray();
        }
        
        $v = new ViewModel(array(
            'by' => $params->get('by', false),
            'hasJobs' => $hasJobs,
        ));
        $v->setTemplate('jobs/sidebar/index');
        $this->layout()->addChild($v, 'sidebar_jobsFilter');
        $repository = $this->getServiceLocator()->get('repositories')->get('job');
        
        $paginator = new \Zend\Paginator\Paginator(
            $repository->getPaginatorAdapter($params->toArray())
        );
        $paginator->setCurrentPageNumber($this->params()->fromQuery('page', 1))
                  ->setItemCountPerPage($params->get('count', 10));
        
        
        
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
        
        return array(
            'by' => $params->get('by', 'all'),
            'jobs' => $paginator
        );
        
    
     }
     
     public function dashboardAction()
     {
         $services = $this->getServiceLocator();
         $myJobs = $services->get('repositories')->get('Job')->fetchRecent($this->auth('id'));
         $allJobs = $services->get('repositories')->get('Job')->fetchRecent();
         
         return array(
             'script' => 'jobs/index/dashboard',
             'type' => $this->params('type'),
             'myJobs' => $myJobs,
             'allJobs' => $allJobs,
         );
     }
    
}
