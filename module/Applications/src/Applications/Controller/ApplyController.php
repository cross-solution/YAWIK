<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Applications controllers */ 
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Applications\Entity\Application;
use Zend\View\Model\ViewModel;
use Auth\Entity\AnonymousUser;
use Zend\View\Model\JsonModel;
use Core\Form\Container;
use Core\Form\SummaryForm;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class ApplyController extends AbstractActionController
{
    
    protected $container;
    
    public function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 10);
    }
    
    public function preDispatch(MvcEvent $e)
    {
        $request      = $this->getRequest();
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $repository   = $repositories->get('Applications/Application');
        $container    = $services->get('forms')->get('Applications/Apply');
        
        if ($request->isPost()) {
            $appId = $this->params()->fromPost('applicationId');
            if (!$appId) {
                throw new \RuntimeException('Missing application id.');
            }
            $application = $repository->find($appId);
            if (!$application) {
                throw new \RuntimeException('Invalid application id.');
            }
            $action     = 'process';
            $routeMatch = $e->getRouteMatch();
            $routeMatch->setParam('action', $action);
            
        } else {
            $application = $repository->create();
        } 
        
        $container->setEntity($application);
        $this->container = $container;
    }
    
    protected function createJobNotFoundModel()
    {
        $this->response->setStatusCode(410);
        $model = new ViewModel(array(
            'content' => /*@translate*/ 'Invalid apply id'
        ));
        $model->setTemplate('auth/index/job-not-found.phtml');
        return $model;
    }
    
    public function indexAction()
    {
        $appId = $this->params('applyId');
        if (!$appId) {
            throw new \RuntimeException('Missing apply id');
        }
        
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        
        $job = $repositories->get('Jobs/Job')->findOneByApplyId($appId);
        
        if (!$job) {
            return $this->createJobNotFoundModel();
        }

        $form = $this->container;
        $application = $form->getEntity();
        $user = $this->auth()->getUser();
        $application->setIsDraft(true);
        $application->setJob($job);
        $application->setContact($user->info);
        if ($user instanceOf AnonymousUser) {
            $application->getPermissions()->grant($user, 'all');
        } else {
            $application->setUser($user);
        }
        
        $repositories->store($application);
        $this->container->setParam('applicationId', $application->id);
        return array(
            'form' => $form
        );
    }
    
    public function processAction()
    {
        $formName  = $this->params()->fromQuery('form');
        $form      = $this->container->getForm($formName);
        $postData  = $form->getOption('use_post_array') ? $_POST : array();
        $filesData = $form->getOption('use_files_array') ? $_FILES : array();
        $data      = array_merge($postData, $filesData);

        $form->setData($data);
        
        if (!$form->isValid()) {
            return new JsonModel(array(
                'valid' => false,
                'errors' => $form->getMessages(),
            ));
        }
        
        if ($form instanceOf SummaryForm) {
            $form->setRenderMode(SummaryForm::RENDER_SUMMARY);
            $viewHelper = 'summaryform';
        } else {
            $viewHelper = 'form';
        }
        
        $this->getServiceLocator()->get('repositories')->store($this->container->getEntity());
        return new JsonModel(array(
            'valid' => $form->isValid(),
            'content' => $this->getServiceLocator()->get('ViewHelperManager')->get($viewHelper)->__invoke($form),
        ));
    }
}
