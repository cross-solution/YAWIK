<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth controller */
namespace Auth\Controller;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Controller for group management.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ManageGroupsController extends AbstractActionController
{
    /**
     * Event identifier for the shared manager.
     * @var string
     */
    protected $eventIdentifier = 'Auth/ManageGroups';
    
    private $formManager;
    
    private $repositories;
    
    private $filterManager;
    
    public function __construct(
    	$formManager,
		$repositories,
		$filterManager
    )
    {
    	$this->formManager = $formManager;
    	$this->repositories = $repositories;
    	$this->filterManager = $filterManager;
    }
	
	static public function factory(ContainerInterface $container)
	{
		return new self(
			$container->get('FormElementManager'),
			$container->get('repositories'),
			$container->get('FilterManager')
		);
	}
    
    /**
     * Register the default events for this controller
     *
     * @internal
     *      Registers two hooks on "onDispatch":
     *      - change action to form and set mode parameter
     *      - inject sidebar navigation
     */
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        
        /*
         * "Redirect" action 'new' and 'edit' to 'form' and set the 
         * route parameter 'mode' to the original action.
         * This must run before onDispatch, because we alter the action param 
         */
        $events->attach(
            MvcEvent::EVENT_DISPATCH,
            function ($event) {
                $routeMatch = $event->getRouteMatch();
                $action     = $routeMatch->getParam('action');
            
                if ('new' == $action || 'edit' == $action) {
                    $routeMatch->setParam('mode', $action);
                    $routeMatch->setParam('action', 'form');
                }
            },
            10
        );
        
        /*
         * Inject a sidebar view model in the Layout-Model, if 
         * the result in the event is not terminal.
         * This must run after "InjectViewModelListener", which runs with
         * a priority of -100.
         */
        $events->attach(
            MvcEvent::EVENT_DISPATCH,
            function ($event) {
                $model = $event->getResult();
                if (!$model instanceof ViewModel || $model->terminate()) {
                    return;
                }
            
                $routeMatch = $event->getRouteMatch();
                $action = $routeMatch->getParam('action');
                if ('form' == $action) {
                    $action = $routeMatch->getParam('mode');
                }
            
                $layout  = $event->getViewModel();
                $sidebar = new ViewModel();
                $sidebar->setVariable('action', $action);
                $sidebar->setTemplate('auth/sidebar/groups-menu');
                $layout->addChild($sidebar, 'sidebar_auth_groups-menu');
            },
            -110
        );

        $serviceLocator  = $this->serviceLocator;
        $defaultServices = $serviceLocator->get('DefaultListeners');
        $events->attach($defaultServices);
    }
    
    /**
     * Index-Action (Group overview)
     */
    public function indexAction()
    {
    }
    
    /**
     * Handles the form.
     *
     * Redirects to index on save success.
     *
     * Expected route parameters:
     * - 'mode': string Either 'new' or 'edit'.
     *
     * Expected query parameters:
     * - 'name' string Name of the group (if mode == 'edit')
     *
     * @return Zend\Stdlib\ResponseInterface|array
     */
    public function formAction()
    {
        $isNew     = 'new' == $this->params('mode');
        $form      = $this->formManager->get('Auth/Group', array('mode' => $this->params('mode')));
        $repository = $this->repositories->get('Auth/Group');
        
        if ($isNew) {
            $group = new \Auth\Entity\Group();
        } else {
            if ($this->getRequest()->isPost()) {
                $data = $this->params()->fromPost('data');
                $id   = isset($data['id']) ? $data['id'] : false;
            } else {
                $id   = $this->params()->fromQuery('id', false);
            }
            if (!$id) {
                throw new \RuntimeException('No id.');
            }
            $group = $repository->find($id);
        }
        
        $form->bind($group);
        if ($this->getRequest()->isPost()) {
            $form->setData($_POST);
            $isOk      = $form->isValid();
            $isUsersOk = !empty($group->users);
             
            if ($isOk) {
                // We have to check here, if there are any users provided
                // as InputFilter does not allow "required" to be set on fieldsets.
                if ($isUsersOk) {
                    if ($isNew) {
                        $user    = $this->auth()->getUser();
                        $group->setOwner($user);
                        $groups  = $user->getGroups();
                        $message = /*@translate*/ 'Group created';
                        $groups->add($group);
                    } else {
                        $message = /*@translate*/ 'Group updated';
                    }
                    
                    $this->notification()->success($message);
                    return $this->redirect()->toRoute('lang/my-groups');
                }
            }
            if (!$isUsersOk) {
                $form->get('data')->get('users')->setNoUsersError(true);
            }
            $this->notification()->error(/*@translate*/ 'Changes not saved.');
        }
        
        return array(
            'form' => $form,
            'isNew' => $isNew,
        );
    }
    
    /**
     * Helper action for userselect form element.
     *
     * @return \Zend\View\Model\JsonModel
     */
    public function searchUsersAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new \RuntimeException('This action must be called via ajax request');
        }
        
        $model = new JsonModel();
        $query = $this->params()->fromPost('query', false);
        if (!$query) {
            $query = $this->params()->fromQuery('q', false);
        }
        if (false === $query) {
            $result = array();
        } else {
            $repositories = $this->repositories;
            $repository   = $repositories->get('Auth/User');
            
            $users = $repository->findByQuery($query);
        
            $userFilter = $this->filterManager->get('Auth/Entity/UserToSearchResult');
            $filterFunc = function ($user) use ($userFilter) {
                return $userFilter->filter($user);
            };
            $result     = array_values(array_map($filterFunc, $users->toArray()));
        }
        
        //$model->setVariable('users', $result);
        $model->setVariables($result);
        return $model;
    }
}
