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
        
         
        $query = $this->listQuery(array(
            'properties_map' => array(
                'jobId'
             ),
            'items_per_page' => 5
        ));
        
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        $mapper = $this->getServiceLocator()->get('ApplicationMapper');

        $applicationsCollection = $mapper->fetchAll($query);
        
        if ($jsonFormat) {
            $viewModel = new JsonModel();
            $hydrator = new ApplicationHydrator();
            $dateStrategy = new ClosureStrategy(
                /*extractFunc*/ 
                function ($value) {
                    if (!$value instanceOf \DateTime) {
                        return null;
                    }
                    return $value->format('Y-m-d H:m:i T');
                },
                /* hydrateFunc */ function($value) { return $value; }
            );
            $hydrator->addStrategy('dateCreated', $dateStrategy)
                     ->addStrategy('dateModified', $dateStrategy);
            
            $items = array();
            foreach ($applicationsCollection as $application) {
                $items[] = $hydrator->extract($application);
            }
            $viewModel->setVariables(array('items' => $items, 'count' => count($items)));
            return $viewModel;
            
        } 
        
        return array(
            'applications' => $applicationsCollection
        );
        
    }
    
    
}
