<?php
/**
 * Cross Applicant Management
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

class CommentController extends AbstractActionController
{
    
    public function onDispatch(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceOf HttpRequest || !$request->isXmlHttpRequest()) {
            //throw new \RuntimeException('This controller must only be called with ajax requests.');
        }
        return parent::onDispatch($event);
    }
    
    public function formAction()
    {
        $services = $this->getServiceLocator();
        $repository = $services->get('repositories')->get('Applications/Application');
        
        $mode  = $this->params()->fromQuery('mode', 'new');
        $appId = $this->params()->fromQuery('id');
        
        if ('edit' == $mode) {
            $comment = $repository->findComment($appId);
        } else {
            $comment = new Comment();
        }
        
        $form = $services->get('forms')->get('Applications/CommentForm');
        $form->bind($comment);
        
        return array(
            'mode' => $mode,
            'identifier' => $appId,
            'commentForm' => $form,
        );
        
    }
    
}

