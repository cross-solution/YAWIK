<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** CommentController.php */
namespace Applications\Controller;

use Core\Factory\ContainerAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Applications\Entity\Comment;
use Zend\View\Model\ViewModel;

/**
 * Controls comment handling on applications
 *
 * @method \Auth\Controller\Plugin\Auth auth()
 */
class CommentController extends AbstractActionController implements ContainerAwareInterface
{
    private $repositories;
    
    private $formManager;
    
    /**
     * Lists comments of an application
     *
     * @return array
     */
    public function listAction()
    {
        $repository = $this->repositories->get('Applications/Application');
        $applicationId = $this->params()->fromQuery('applicationId', 0);
        $application = $repository->find($applicationId); /* @var \Applications\Entity\Application $application */
        
        $this->acl($application, 'read');
        
        return array(
            'comments' => $application->getComments(),
        );
    }
    
    /**
     * Processes formular data
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function formAction()
    {
        $repository = $this->repositories->get('Applications/Application');
        
        $mode  = $this->params()->fromQuery('mode', 'new');
        $appId = $this->params()->fromQuery('id');
        /* @var \Applications\Entity\Application $application */
        $application = $repository->find($appId);

        $viewModel = new ViewModel();
        if ('edit' == $mode) {
            $comment = $repository->findComment($appId);
        } else {
            $comment = new Comment();
            $comment->setUser($this->auth()->getUser());
        }
        
        $this->acl($application, 'read');
        
        $form = $this->formManager->get('Applications/CommentForm');
        $form->bind($comment);
        
        if ($this->getRequest()->isPost()) {
            $form->setData($_POST);
            
            if ($form->isValid()) {
                if ('new' == $mode) {
                    $application = $repository->find($appId);
                    $application->getComments()->add($comment);
                    $application->changeStatus(
                        $application->getStatus(),
                        sprintf(
                                    /* @translate */ 'Application was rated by %s',
                                     $this->auth()->getUser()->getInfo()->getDisplayName()
                    )
                        );
                }
                $viewModel->setVariable('isSaved', true);
            }
        }
       
        $viewModel->setVariables(
            array(
            'mode' => $mode,
            'identifier' => $appId,
            'commentForm' => $form,
            )
        );
        return $viewModel;
    }
    
    public function setContainer(ContainerInterface $container)
    {
        $this->repositories = $container->get('repositories');
        $this->formManager = $container->get('forms');
    }
    
    /**
     * @param ContainerInterface $container
     *
     * @return CommentController
     */
    public static function factory(ContainerInterface $container)
    {
        $ob = new self();
        $ob->setContainer($container);
        return $ob;
    }
}
