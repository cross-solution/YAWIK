<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** CommentController.php */ 
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Applications\Entity\Comment;
use Applications\Entity\Application;
use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

/**
 * Controls comment handling on applications
 */
class CommentController extends AbstractActionController
{
    
    /**
     * (non-PHPdoc)
     * @see \Zend\Mvc\Controller\AbstractActionController::onDispatch()
     */
    public function onDispatch(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceOf HttpRequest || !$request->isXmlHttpRequest()) {
            //throw new \RuntimeException('This controller must only be called with ajax requests.');
        }
        return parent::onDispatch($event);
    }
    
    /**
     * Lists comments of an application
     * 
     * @return multitype:NULL
     */
    public function listAction()
    {
        $repository = $this->getServiceLocator()->get('repositories')->get('Applications/Application');
        $applicationId = $this->params()->fromQuery('applicationId', 0);
        $application = $repository->find($applicationId);
        
        $this->acl($application, 'read');
        
        return array(
            'comments' => $application->comments,
        );
    }
    
    /**
     * Processes formular data
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function formAction()
    {
        $services = $this->getServiceLocator();
        $repository = $services->get('repositories')->get('Applications/Application');
        
        $mode  = $this->params()->fromQuery('mode', 'new');
        $appId = $this->params()->fromQuery('id');

        $viewModel = new ViewModel();
        if ('edit' == $mode) {
            //$application = $repository->findByCommentId($appId);
            $comment = $repository->findComment($appId);
        } else {
            $comment = new Comment();
            $comment->setUser($this->auth()->getUser());
        }
        
        $this->acl($application, 'read');
        
        $form = $services->get('forms')->get('Applications/CommentForm');
        $form->bind($comment);
        
        if ($this->getRequest()->isPost()) {
            $form->setData($_POST);
            
            if ($form->isValid()) {
                if ('new' == $mode) {
                    $application = $repository->find($appId);
                    $application->comments->add($comment);
                }
                $viewModel->setVariable('isSaved', true);
            }
            
            
        }
       
        $viewModel->setVariables(array(
            'mode' => $mode,
            'identifier' => $appId,
            'commentForm' => $form,
        ));
        return $viewModel;
        
    }
}