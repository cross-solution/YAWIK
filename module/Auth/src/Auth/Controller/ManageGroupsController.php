<?php
/**
 * Cross Applicant Management
 * 
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth controller */
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Main Action Controller for Authentication module.
 *
 */
class ManageGroupsController extends AbstractActionController
{

    protected $eventIdentifier = 'Auth/ManageGroups';
    
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        
        /* This must run before onDispatch, because we could alter the action param */
        $events->attach(MvcEvent::EVENT_DISPATCH, function($event) {
            $routeMatch = $event->getRouteMatch();
            $action     = $routeMatch->getParam('action');
            
            if ('new' == $action || 'edit' == $action) {
                $routeMatch->setParam('mode', $action);
                $routeMatch->setParam('action', 'form');
            }
        }, 10);
        
        $events->attach(MvcEvent::EVENT_DISPATCH, function($event) {
            $model = $event->getResult();
            if (!$model instanceOf ViewModel || $model->terminate()) {
                return;
            }
            
            $layout  = $event->getViewModel();
            $sidebar = new ViewModel(); 
            $sidebar->setTemplate('auth/sidebar/groups-menu');
            $layout->addChild($sidebar, 'sidebar_auth_groups-menu');
        }, -110);
    }
    
    public function indexAction()
    { }
    
    public function formAction()
    {
        $isNew     = 'new' == $this->params('mode');
        $hasErrors = false;
        $services  = $this->getServiceLocator();
        $form      = $services->get('formelementmanager')->get('Auth/Group', array('mode' => $this->params('mode')));
        
        $group = $isNew 
               ? new \Auth\Entity\Group()
               : $this->auth()->getUser()->getGroup($this->params()->fromQuery('name'));
        
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
                        $groups  = $user->getGroups();
                        $message = /*@translate*/ 'Group created'; 
                        $groups->add($group);
                    } else {
                        $message = /*@translate*/ 'Group updated';
                    }
                    $this->flashMessenger()->addMessage($message);
                    return $this->redirect()->toRoute('lang/my-groups');
                }
            }
            if (!$isUsersOk) {
                $form->get('data')->get('users')->setNoUsersError(true);
            }
            $hasErrors = true;
        }
        
        return array(
            'form' => $form,
            'isNew' => $isNew,
            'hasErrors' => $hasErrors, 
        );
    }
    
    public function searchUsersAction()
    {
        $model = new JsonModel();
        $query = $this->params()->fromPost('query', false);
        if (false === $query) {
            $result = array();
        } else {
            $services     = $this->getServiceLocator();
            $repositories = $services->get('repositories');
            $repository   = $repositories->get('Auth/User');
            
            $users = $repository->findByQuery($query);
        
            $filter = $services->get('filtermanager')->get('Auth/Entity/JsonSearchResult');
            $result = array_values(array_map(function($user) use ($filter) { return $filter->filter($user); }, $users->toArray()));
        }
        
        $model->setVariable('users', $result);
        return $model;
    }
    
}

 
