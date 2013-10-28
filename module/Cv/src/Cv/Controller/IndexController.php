<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Core */
namespace Cv\Controller;

use Zend\Mvc\Controller\AbstractActionController;


/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class IndexController extends AbstractActionController
{
    
    /**
     * Home site
     *
     */
    public function indexAction()
    {
        $repository = $this->getServiceLocator()->get('repositories')->get('cv');
        $userId = $this->auth('id');
        if (!$userId) $userId = 0;
        
        $params = array_merge(
            $this->params()->fromQuery(),
            array('userId' => $userId)
        );
        
        
        
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
                'items' => $this->getServiceLocator()->get('builders')->get('JsonCv')
                                ->unbuildCollection($paginator->getCurrentItems()),
                'count' => $paginator->getTotalItemCount()
            ));
            return $viewModel;
        
        }
        
        return array(
            'resumes' => $paginator,
            'sort' => isset($params['sort']) ? $params['sort'] : 'none',
        );
        
    	
    }
    
}
