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
        $repository = $this->getServiceLocator()->get('ApplicationRepository');
        
        $paginator = new \Zend\Paginator\Paginator(
            $repository->getPaginatorAdapter(
                $this->params()->fromQuery(),
                array('lastname' => 1)
            )
        );
        $paginator->setCurrentPageNumber($this->params()->fromQuery('page'))
                  ->setItemCountPerPage(10);
        
        
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        
        if ($jsonFormat) {
            $viewModel = new JsonModel();
            $items = iterator_to_array($paginator);
            
            $viewModel->setVariables(array('items' => $repository->getApplicationBuilder()->unbuildCollection($paginator->getCurrentItems()), 'count' => $paginator->getTotalItemCount()));
            return $viewModel;
            
        } 
        
        return array(
            'applications' => $paginator
        );
        
    }
    
    
}
