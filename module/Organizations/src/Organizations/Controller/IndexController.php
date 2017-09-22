<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Organizations */
namespace Organizations\Controller;

use Core\Entity\Collection\ArrayCollection;
use Core\Form\SummaryForm;
use Organizations\Exception\MissingParentOrganizationException;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Organizations\Repository;
use Organizations\Form;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Response;
use Core\Entity\Exception\NotFoundException;

/**
 * Main Action Controller for the Organization.
 * Responsible for handling the organization form.
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Rafal Ksiazek
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @author Anthonius Munthi <me@itstoni.com>
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
	 * @var FormElementManagerV3Polyfill
	 */
    private $formManager;
    
    private $viewHelper;
	
	/**
	 * @var TranslatorInterface
	 */
    private $translator;
	
	/**
	 * Create new controller instance
	 *
	 * @param Form\Organizations $form
	 * @param Repository\Organization $repository
	 * @param TranslatorInterface $translator
	 * @param $formManager
	 * @param $viewHelper
	 */
    public function __construct(
    	Form\Organizations $form,
	    Repository\Organization $repository,
		TranslatorInterface $translator,
		$formManager,
		$viewHelper
    )
    {
        $this->repository = $repository;
        $this->form = $form;
        $this->formManager = $formManager;
        $this->viewHelper = $viewHelper;
        $this->translator = $translator;
    }
	
    /**
     * Generates a list of organizations
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->pagination([
            'paginator' => [
                'Organizations/Organization',
                'as' => 'organizations'
            ],
            'form' => [
                'Core/Search',
                [
                    'text_name' => 'text',
                    'text_placeholder' => /*@translate*/ 'Search for organizations',
                    'button_element' => 'text'
                ],
                'as' => 'form'
            ]
        ]);
    }
     
     
    /**
     * Change (Upsert) organizations
     *
     * @return JsonModel|array
     * @throws \RuntimeException
     */
    public function editAction()
    {
        /* @var $request \Zend\Http\Request */
        $translator      = $this->translator;
        $return          = null;
        $request         = $this->getRequest();
        $params          = $this->params();
        $formIdentifier  = $params->fromQuery('form');

        try {
            /* @var $handler \Organizations\Controller\Plugin\GetOrganizationHandler */
            $handler = $this->plugin('Organizations/GetOrganizationHandler');
            $org  = $handler->process($this->params(), true);
        } catch (MissingParentOrganizationException $e) {
            return $this->getErrorViewModel('no-parent');
        } catch (NotFoundException $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return [
                'message' => sprintf($translator->translate('Organization with id "%s" not found'), $e->getId()),
                'exception' => $e
            ];
        }

        $container       = $this->getFormular($org);

        if (isset($formIdentifier) && $request->isPost()) {
            /* @var $form \Zend\Form\FormInterface */
            $postData = $this->params()->fromPost();
            $filesData = $this->params()->fromFiles();
            /* due to issues in ZF2 we need to clear the employees collection in the entity,
             * prior to binding. Otherwise it is not possible to REMOVE an employee, as the
             * MultiCheckbox Validation will FAIL on empty values!
             */
            if ("employeesManagement" == $formIdentifier) {
                $markedEmps = array();
                // Check if no permissions are set, and set one, mark this employee and restore it afterwards.
                foreach ($postData['employees']['employees'] as &$empData) {
                    if (!isset($empData['permissions'])) {
                        $empData['permissions'][] = 16;
                        $markedEmps[] = $empData['user'];
                    }
                }
                $org->setEmployees(new ArrayCollection());
            }

            $form = $container->get($formIdentifier);
            $form->setData(array_merge($postData, $filesData));
            if (!isset($form)) {
                throw new \RuntimeException('No form found for "' . $formIdentifier . '"');
            }
            $isValid = $form->isValid();

            if ("employeesManagement" == $formIdentifier) {
                // remove permissions from marked employees
                foreach ($org->getEmployees() as $emp) {
                    $empId = $emp->getUser()->getId();
                    if (in_array($empId, $markedEmps)) {
                        $emp->getPermissions()->revokeAll();
                    }
                }
            }
            if ($isValid) {
                $orgName = $org->getOrganizationName();
                if ($orgName && '' !== (string) $orgName->getName()) {
                    $org->setIsDraft(false);
                }
                $this->repository->store($org);
            }

            $organization = $container->getEntity();
            $this->repository->store($organization);

            if ('file-uri' === $this->params()->fromPost('return')) {
                /* @var $hydrator \Core\Entity\Hydrator\FileCollectionUploadHydrator
                 * @var $file     \Organizations\Entity\OrganizationImage */
                $basepath = $this->viewHelper->get('basepath');
                $hydrator = $form->getHydrator();
                $file     = $hydrator->getLastUploadedFile();
                $content = $basepath($file->getUri());
            } else {
                if ($form instanceof SummaryForm) {
                    /* @var $form \Core\Form\SummaryForm */
                    $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
                    $viewHelper = 'summaryForm';
                } else {
                    $viewHelper = 'form';
                }
                
                $content = $this->viewHelper->get($viewHelper)->__invoke($form);
            }

            return new JsonModel(
                array(
                'valid' => $isValid,
                'content' => $content,
                )
            );
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
        //$services  = $this->serviceLocator;
        $forms     = $this->formManager;
        $container = $forms->get(
            'Organizations/Form',
            array(
            'mode' => $organization->getId() ? 'edit' : 'new'
            )
        );
        $container->setEntity($organization);
        $container->setParam('id', $organization->getId());
//        $container->setParam('applyId',$job->applyId);

        if ('__my__' != $this->params('id', '')) {
            $container->disableForm('employeesManagement')
                        ->disableForm('workflowSettings');
        } else {
            $container ->disableForm('organizationLogo')
                        ->disableForm('descriptionForm');
        }
        return $container;
    }

    protected function getErrorViewModel($script)
    {
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

        $model = new ViewModel();
        $model->setTemplate("organizations/error/$script");

        return $model;
    }
}
