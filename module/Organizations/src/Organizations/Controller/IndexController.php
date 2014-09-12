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
        $services     = $this->getServiceLocator();
        $params      = $this->getRequest()->getQuery();
        $jsonFormat  = 'json' == $params->get('format');
        $repository  = $services->get('repositories')->get('Organizations/Organization');
        $isRecruiter = $this->acl()->isRole('recruiter');
        
        
        $params = $this->getRequest()->getQuery();
        $isRecruiter = $this->acl()->isRole('recruiter');
        if ($isRecruiter) {
            $params->set('by', 'me');
        }
         
         //default sorting
        if (!isset($params['sort'])) {
            $params['sort']="-date";
        }
        $params->count = 25;
        // save the Params in the Session-Container
        $this->paginationParams()->setParams('Organizations\Index', $params);
        $paginator = $this->paginator('Organizations/Organization',$params);
     
        return array(
            'script' => 'organizations/index/list',
            'organizations' => $paginator
        );
        
        
        return $return;
     }
     
     
    /**
     * Change (Upsert) organisations
     */
    public function editAction()
    { 
        $services     = $this->getServiceLocator();
        $params      = $this->getRequest()->getQuery();
        $jsonFormat  = 'json' == $params->get('format');
        $repository  = $services->get('repositories')->get('Organizations/Organization');
        $isRecruiter = $this->acl()->isRole('recruiter');
        
        //$org = $repository->find("53f1f1188dc1b3bd1b149fef");
        $org = $repository->find("53f1f1188dc1b3bd1b149fef");
        $form    = $services->get('forms')->get('organizations/form');
        $form->bind($org);
        
        $return = array(
            'form' => $form
        );
        return $return;
     }
     
    /** 
     * Test Repository and Entities
     */
    public function testfillAction()
    {
        /*
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
        
        $repName = $services->get('repositories')->get('Organizations/OrganizationName');
        $rep = $services->get('repositories')->get('Organizations/Organization');
        
        // Create a new Entry in 3 Steps
        $entity = $rep->findbyRef('abcdef');
        $name = $repName->findbyName("Bonn");
        $entity->setOrganizationName($name3);
        */
        
    }
    
    /** 
     * companyLogo
     */
    public function logoAction()
    {
        $response = $this->getResponse();
        $file     = $this->getFile();
        
        if (!$file) {
            return $response;
        }
        
        //$this->acl($file);
        
        $response->getHeaders()->addHeaderline('Content-Type', $file->type)
                               ->addHeaderline('Content-Length', $file->length);
        $response->sendHeaders();
        $resource = $file->getResource();
        while (!feof($resource)) {
            echo fread($resource, 1024);
        }
        return $response;
    }
    
    protected function getFile()
    {
        $imageId = $this->params('id');
        $response = $this->getResponse();
        
        try {
            $repository = $this->getServiceLocator()->get('repositories')->get('Organizations/OrganizationImage');
            
            $file       = $repository->find($imageId);                
            if ($file) {
                return $file;
            }
            $response->setStatusCode(404);
        } catch (\Exception $e) {
            $response->setStatusCode(404);
            $this->getEvent()->setParam('exception', $e);
            return;
        }
        return;
    }
}
