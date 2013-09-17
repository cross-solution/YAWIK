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
use Applications\Model\Hydrator\ApplicationHydrator;
use Applications\Model\JsonApplication;
use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;
use Core\Mapper\Criteria\Criteria;
use Core\Filter\ListQuery;
use Core\Mapper\Query\Query;



/**
 * Action Controller for managing applications.
 *
 */
class ManageController extends AbstractActionController
{
    
    /**
     * List applications
     *
     */
    public function indexAction()
    { 
        
        $v = new ViewModel(array(
            'by' => $this->params()->fromQuery('by', 'me'),
            'hasJobs' => (bool) $this->getServiceLocator()
                                     ->get('repositories')
                                     ->get('job')
                                     ->countByUser($this->auth('id'))
        ));
        $v->setTemplate('applications/sidebar/manage');
        $this->layout()->addChild($v, 'sidebar_applicationsFilter');
        $repository = $this->getServiceLocator()->get('repositories')->get('application');
        $params = $this->params()->fromQuery();
        
            
        
        $paginator = new \Zend\Paginator\Paginator(
            $repository->getPaginatorAdapter($params)
        );
        $paginator->setCurrentPageNumber($this->params()->fromQuery('page'))
                  ->setItemCountPerPage(10);
        
        
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        
        if ($jsonFormat) {
            $viewModel = new JsonModel();
            //$items = iterator_to_array($paginator);
            
            $viewModel->setVariables(array(
                'items' => $this->getServiceLocator()->get('builders')->get('JsonApplication')
                                ->unbuildCollection($paginator->getCurrentItems()),
                'count' => $paginator->getTotalItemCount()
            ));
            return $viewModel;
            
        } 
        
        return array(
            'applications' => $paginator,
            'byJobs' => isset($params['by']) && 'jobs' == $params['by'],
            'sort' => isset($params['sort']) ? $params['sort'] : 'none',
            
        );
        
        
    }
    
    public function detailAction(){

    	$application = $this->getServiceLocator()
    						->get('repositories')
    						->get('application')->find($this->params('id'), 'EAGER');
    	
    	$jsonFormat = 'json' == $this->params()->fromQuery('format');
    	if ($jsonFormat) {
    		$viewModel = new JsonModel();
    		$viewModel->setVariables(array('educations' => $application->cv->educations));
    		return $viewModel;
    	}

    	return array('application'=> $application);
    }
    
}
