<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Jobs */
namespace Organizations\Controller;

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
     * List organisations
     */
    public function indexAction()
    { 
        
        $params      = $this->getRequest()->getQuery();
        $jsonFormat  = 'json' == $params->get('format');
        $repository  = $this->getServiceLocator()->get('repositories')->get('Jobs/Job');
        $isRecruiter = $this->acl()->isRole('recruiter');
        
        // @TODO: Jobs/IndexController
        $return = array();
        return $return;
     }
}
