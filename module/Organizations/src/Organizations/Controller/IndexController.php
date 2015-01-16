<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Organizations */
namespace Organizations\Controller;

use Core\Form\SummaryForm;
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
     * attaches further Listeners for generating / processing the output
     *
     * @return $this
     */
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $serviceLocator  = $this->getServiceLocator();
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events          = $this->getEventManager();
        $events->attach($defaultServices);
        return $this;
    }

    /**
     * Generates a list of organizations
     *
     * @return array
     */
    public function indexAction()
    { 
        $params        = $this->getRequest()->getQuery();
        $isRecruiter   = $this->acl()->isRole('recruiter');
        $params->count = 10;
        if ($isRecruiter) {
            $params->set('by', 'me');
        }
         //default sorting
        if (!isset($params['sort'])) {
            $params->set('sort', "-name");
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
     * Change (Upsert) organizations
     */
    public function editAction()
    {
        $return          = Null;
        $services        = $this->getServiceLocator();
        $request         = $this->getRequest();
        $ajaxRequest     = $request->isXmlHttpRequest();
        $params          = $this->params();
        $formIdentifier  = $params->fromQuery('form');
        $id_fromRoute    = $this->params('id', 0);
        $id_fromSubForm  = $this->params()->fromPost('id',0);
        $organization_id = empty($id_fromRoute)?$id_fromSubForm:$id_fromRoute;
        $repositories    = $services->get('repositories');
        $repository      = $repositories->get('Organizations/Organization');
        $container       = $services->get('forms')->get('organizations/form');
        $viewHelper      = $services->get('ViewHelperManager');
        $org             = $repository->find($organization_id);
        if (!isset($org) && !$request->isPost()) {
            // create a new Organization
            $org =  $repository->create();
            $org->setIsDraft(true);
            $user  = $this->auth()->getUser();
            $permissions = $org->getPermissions();
            $permissions->grant($user, PermissionsInterface::PERMISSION_ALL);
            $repositories->persist($org);
        }
        if (isset($org)) {
            if (isset($org->org) && !empty($org->organizationName->name)) {
                $org->setIsDraft(false);
            }
            $container->setEntity($org);
            $container->setParam('id', $org->id);
            if (isset($formIdentifier) && $request->isPost()) {
                $postData = $this->params()->fromPost();
                $filesData = $this->params()->fromFiles();
                $form = $container->get($formIdentifier);
                $form->setData(array_merge($postData, $filesData));
                if (!isset($form)) {
                    throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
                }
                $isValid = $form->isValid();
                if ($isValid) {
                    //$user  = $this->auth()->getUser();
                    //$permissions = $org->getPermissions();
                    //$permissions->grant($user, PermissionsInterface::PERMISSION_CHANGE);
                    //$repositories->persist($org);
                }

                $organization = $container->getEntity();
                $this->getServiceLocator()->get('repositories')->store($organization);

                if ('file-uri' === $this->params()->fromPost('return')) {
                    $basepath = $this->getServiceLocator()->get('ViewHelperManager')->get('basepath');
                    $content = $basepath($form->getHydrator()->getLastUploadedFile()->getImageUri());
                } else {
                    if ($form instanceOf SummaryForm) {
                        //$form->setRenderMode(SummaryForm::RENDER_SUMMARY);
                        $viewHelper = 'summaryform';
                    } else {
                        $viewHelper = 'form';
                    }
                    $content = $this->getServiceLocator()->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
                }

                return new JsonModel(array(
                    'valid' => $form->isValid(),
                    'content' => $content,
                ));
            }
        }

        if (!isset($return)) {
            $return = array(
                'form' => $container
            );
        }
        return $return;
     }

    /**
     * returns an organization logo.
     * @deprecated
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
     * @deprecated: gets a image-logo from the database, use the core-action instead
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
