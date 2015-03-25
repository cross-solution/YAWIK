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

use Auth\Exception\UnauthorizedAccessException;
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
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Rafal Ksiazek
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 * @method \Acl\Controller\Plugin\Acl acl()
 * @method \Core\Controller\Plugin\PaginationParams paginationParams()
 * @method \Core\Controller\Plugin\CreatePaginator paginator(string $repositoryName, array $defaultParams = array(), bool $usePostParams = false)
 * @method \Auth\Controller\Plugin\Auth auth()
 */
class IndexController extends AbstractActionController
{
    /**
     * The organization form.
     *
     * @var Form\Organizations
     */
    private $form;

    /**
     * The organization repository.
     *
     * @var Repository\Organization
     */
    private $repository;

    /**
     * Creates an instance.
     *
     * @param Form\Organizations      $form         Organization form.
     * @param Repository\Organization $repository   Organization repository
     */
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
        /* @var $request \Zend\Http\Request */
        $request       = $this->getRequest();
        $params        = $request->getQuery();
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
     *
     * @return JsonModel
     * @throws \RuntimeException
     */
    public function editAction()
    {
        /* @var $request \Zend\Http\Request */
        $serviceLocator  = $this->getServiceLocator();
        $return          = Null;
        $request         = $this->getRequest();
        $params          = $this->params();
        $formIdentifier  = $params->fromQuery('form');
        $org             = $this->getOrganization(true);
        $container       = $this->getFormular($org);

        if (isset($formIdentifier) && $request->isPost()) {

            /* @var $form \Zend\Form\FormInterface */
            $postData = $this->params()->fromPost();
            $filesData = $this->params()->fromFiles();
            $form = $container->get($formIdentifier);
            $form->setData(array_merge($postData, $filesData));
            if (!isset($form)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            $isValid = $form->isValid();
            if ($isValid) {
                $orgName = $org->getOrganizationName();
                if ($orgName && '' !== (string) $orgName->getName()) {
                    $org->setIsDraft(false);
                }
                $serviceLocator->get('repositories')->persist($org);
            }

            $organization = $container->getEntity();
            $serviceLocator->get('repositories')->store($organization);

            if ('file-uri' === $this->params()->fromPost('return')) {

                /* @var $hydrator \Core\Entity\Hydrator\FileCollectionUploadHydrator
                 * @var $file     \Organizations\Entity\OrganizationImage */
                $basepath = $serviceLocator->get('ViewHelperManager')->get('basepath');
                $hydrator = $form->getHydrator();
                $file     = $hydrator->getLastUploadedFile();
                $content = $basepath($file->getUri());
            } else {
                if ($form instanceOf SummaryForm) {
                    /* @var $form \Core\Form\SummaryForm */
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
     * Gets the organization form container.
     *
     * @param \Organizations\Entity\OrganizationInterface $organization
     *
     * @return \Organizations\Form\Organizations
     */
    protected function getFormular($organization)
    {
        /* @var $container \Organizations\Form\Organizations */
        $services  = $this->getServiceLocator();
        $forms     = $services->get('FormElementManager');
        $container = $forms->get('organizations/form', array(
            'mode' => $organization->getId() ? 'edit' : 'new'
        ));
        $container->setEntity($organization);
        $container->setParam('id',$organization->id);
//        $container->setParam('applyId',$job->applyId);

        if ('__my__' != $this->params('id', '')) {
            $container->disableForm('employeesManagement');
        } else {
            $container->disableForm('organizationLogo')
                      ->disableForm('descriptionForm');
        }
        return $container;
    }

    /**
     * Gets the organization entity.
     *
     * @param bool $allowDraft
     *
     * @return \Organizations\Entity\Organization
     * @throws \RuntimeException
     */
    protected function getOrganization($allowDraft = true)
    {
        $services       = $this->getServiceLocator();
        $repositories   = $services->get('repositories');

        // @TODO three different method to obtain the job-id ?, simplify this
        $id_fromRoute = $this->params('id', 0);
        $id_fromSubForm = $this->params()->fromPost('id',0);
        $user = $this->auth()->getUser(); /* @var $user \Auth\Entity\UserInterface */

        /* @var $organizationId string */
        $organizationId = empty($id_fromRoute)?$id_fromSubForm:$id_fromRoute;
        $editOwnOrganization = '__my__' === $organizationId;
        if ($editOwnOrganization) {
            /* @var $userOrg \Organizations\Entity\OrganizationReference */
            $userOrg = $user->getOrganization();
            if ($userOrg->hasAssociation() && !$userOrg->isOwner()) {
                throw new UnauthorizedAccessException('You may not edit this organization as you are only employer.');
            }
            $organizationId = $userOrg->hasAssociation() ? $userOrg->getId() : 0;
        }

        if (empty($organizationId) && $allowDraft) {
            /* @var $organization \Organizations\Entity\Organization */
            $organization = $this->repository->findDraft($user);
            if (empty($organization)) {
                $organization = $this->repository->create();
                $organization->setIsDraft(true);
                $organization->setUser($user);
                if (!$editOwnOrganization) {
                    /* @var $parent \Organizations\Entity\OrganizationReference */
                    $parent = $user->getOrganization();
                    if (!$parent->hasAssociation()) {
                        throw new \RuntimeException('You cannot create organizations, because you do not belong to a parent organization. Use "User menu -> create my organization" first.');
                    }
                    $organization->setParent($parent->getOrganization());
                }

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
