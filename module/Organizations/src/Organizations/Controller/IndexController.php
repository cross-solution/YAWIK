<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Organizations */
namespace Organizations\Controller;

use Core\Form\SummaryForm;
use Zend\Mvc\Controller\AbstractActionController;
use Organizations\Repository;
use Organizations\Form;
use Zend\Session\Container as Session;
use Zend\View\Model\JsonModel;
use Core\Entity\PermissionsInterface;

/**
 * Main Action Controller for the Organization.
 * Responsible for handling the organization form.
 */
class IndexController extends AbstractActionController
{
    /**
     * @var Form\Organizations
     */
    private $form;

    /**
     * @var Repository\Organization
     */
    private $repository;

    public function __construct(Form\Organizations $form, Repository\Organization $repository)
    {
        $this->repository = $repository;
        $this->form = $form;
    }
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
        $serviceLocator  = $this->getServiceLocator();
        $return          = Null;
        $request         = $this->getRequest();
        $params          = $this->params();
        $formIdentifier  = $params->fromQuery('form');
        $org             = $this->getOrganization(true);
        $container       = $this->getFormular($org);

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
                if (isset($org->organizationName) && !empty($org->organizationName->name)) {
                    $org->setIsDraft(false);
                }
                $serviceLocator->get('repositories')->persist($org);
            }

            $organization = $container->getEntity();
            $serviceLocator->get('repositories')->store($organization);

            if ('file-uri' === $this->params()->fromPost('return')) {
                $basepath = $serviceLocator->get('ViewHelperManager')->get('basepath');
                $content = $basepath($form->getHydrator()->getLastUploadedFile()->getUri());
            } else {
                if ($form instanceOf SummaryForm) {
                    $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
                    $viewHelper = 'summaryform';
                } else {
                    $viewHelper = 'form';
                }
                $content = $serviceLocator->get('ViewHelperManager')->get($viewHelper)->__invoke($form);
            }

            return new JsonModel(array(
                'valid' => $form->isValid(),
                'content' => $content,
            ));
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
            $file = $this->repository->find($imageId);
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

    protected function getFormular($organization)
    {
        $services = $this->getServiceLocator();
        $forms    = $services->get('FormElementManager');
        $container = $forms->get('organizations/form', array(
            'mode' => $organization->id ? 'edit' : 'new'
        ));
        $container->setEntity($organization);
        $container->setParam('id',$organization->id);
//        $container->setParam('applyId',$job->applyId);
        return $container;
    }

    protected function getOrganization($allowDraft = true)
    {
        $services       = $this->getServiceLocator();
        $repositories   = $services->get('repositories');

        // @TODO three different method to obtain the job-id ?, simplify this
        $id_fromRoute = $this->params('id', 0);
        $id_fromSubForm = $this->params()->fromPost('id',0);
        $user = $this->auth()->getUser();

        $organizationId = empty($id_fromRoute)?$id_fromSubForm:$id_fromRoute;

        if (empty($organizationId) && $allowDraft) {
            $organization = $this->repository->findDraft($user);
            if (empty($organization)) {
                $organization = $this->repository->create();
                $organization->setIsDraft(true);
                $organization->setUser($user);
                $permissions = $organization->getPermissions();
                $permissions->grant($user, PermissionsInterface::PERMISSION_ALL);
                $repositories->store($organization);
            }
            return $organization;
        }

        $organization      = $this->repository->find($organizationId);
        if (!$organization) {
            throw new \RuntimeException('No Organization found with id "' . $organizationId . '"');
        }
        return $organization;
    }
}
