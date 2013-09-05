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
        
        $repository = $this->getServiceLocator()->get('repositories')->get('job');
        
        $paginator = new \Zend\Paginator\Paginator(
            $repository->getPaginatorAdapter(
                $this->params()->fromQuery(),
                array('applyId' => 1)
            )
        );
        $paginator->setCurrentPageNumber($this->params()->fromQuery('page'))
                  ->setItemCountPerPage(10);
        
        
        
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
            'jobs' => $paginator
        );
        
    
     }
    
}
