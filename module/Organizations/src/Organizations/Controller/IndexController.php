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
use Zend\Stdlib\Parameters;

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
        
        // @TODO: look at Jobs/IndexController
        $return = array();
        return $return;
     }
     
    /** 
     * Test Repository and Entities
     */
    public function testfillAction()
    {
        $services = $this->getServiceLocator();
        $config = $services->get('Config');
        
          $this->request->setMethod('post');
            $params = new Parameters(array(
                'name' => 'doda dola',
            ));
            $this->getRequest()->setPost($params);
            
            $p = $this->params()->fromPost();
        //$services->get('Log/Core/Cam')->info('Jobs/manage/saveJob ' . var_export($p, True));
        $user = $services->get('AuthenticationService')->getUser();
        
        $entity = $services->get('repositories')->get('Organizations/Organization')->create();
        //$entity = $services->get('repositories')->get('Jobs/Job')->findOneBy(array("applyId" => (string) $applyId));
        //if (!isset($entity)) {
        //$entity = $services->get('repositories')->get('Jobs/Job')->create(array("applyId" => (string) $applyId))
        //$entity->setUser($user);
        //$services->get('repositories')->get('Organizations/Organization')->store($entity);
    }
}
