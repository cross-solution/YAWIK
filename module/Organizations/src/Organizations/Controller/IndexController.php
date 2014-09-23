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
use Zend\Session\Container as Session;
use Zend\View\Model\JsonModel;
use Core\Entity\PermissionsInterface;

/**
 * Main Action Controller for the Organization.
 * Responsible for displaying the home site.  
 *
 */
class IndexController extends AbstractActionController
{
    /**
     * List organisations
     * @return array
     */
    public function indexAction()
    { 
        $params        = $this->getRequest()->getQuery();
        $isRecruiter   = $this->acl()->isRole('recruiter');
        $params->count = 25;
        if ($isRecruiter) {
            $params->set('by', 'me');
        }
         //default sorting
        if (!isset($params['sort'])) {
            $params['sort']="-date";
        }
        // save the Params in the Session-Container
        $this->paginationParams()->setParams('Organizations\Index', $params);
        $paginator = $this->paginator('Organizations/Organization',$params);
        return array(
            'script' => 'organizations/index/list',
            'organizations' => $paginator
        );
     }
     
     
    /**
     * Change (Upsert) organisations
     */
    public function editAction()
    {
        $return          = Null;
        $services        = $this->getServiceLocator();
        $request         = $this->getRequest();
        $ajaxRequest     = $request->isXmlHttpRequest();
        $organization_id = $this->params('id', 0);
        $repositories    = $services->get('repositories');
        $repository      = $repositories->get('Organizations/Organization');
        $form            = $services->get('forms')->get('organizations/form');
        $viewHelper      = $services->get('ViewHelperManager');
        $org             = $repository->find($organization_id);
        if (isset($org)) {
            $form->bind($org);
            if ($request->isPost()) {
                $postData = $this->params()->fromPost();
                $form->setData($postData);
                $isValid = $form->isValid() || True;
                if ($isValid) {
                    //$org = $hydrator->hydrate($postData, $org);
                    //$org = $hydrator->hydrate($org, $entityOrganizationFromDB);
                    //$services->get('repositories')->store($entityOrganization);
                    $user  = $this->auth()->getUser();
                    $permissions = $org->getPermissions();
                    $permissions->grant($user, PermissionsInterface::PERMISSION_CHANGE);
                    //->revoke($this->auth()->getUser(), PermissionsInterface::PERMISSION_CHANGE)
                    //->inherit($application->getJob()->getPermissions());
                    $repositories->store($org);
                }
                if ($ajaxRequest) {
                    $summeryFormViewHelper = $viewHelper->get('summaryform');
                    //$form->setRenderMode(SummaryForm::RENDER_SUMMARY);
                    $content = $summeryFormViewHelper->__invoke($form);
                    $return =  new JsonModel(array(
                        'valid' => True,
                        'content' => $content
                    ));
                }
            }
        }

        if (!isset($return)) {
            $return = array(
                'form' => $form
            );
        }
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
     * @return \Zend\Stdlib\ResponseInterface
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

    /**
     * deprecated: gets a image-logo from the database, use the core-action instead
     * @return mixed
     */
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
            return Null;
        }
        return Null;
    }
}
