<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Controller;

use Auth\AuthenticationService;
use Auth\Exception\UnauthorizedAccessException;
use Auth\Form\UserPassword;
use Core\Repository\RepositoryService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class PasswordController extends AbstractActionController
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var UserPassword
     */
    private $form;

    /**
     * @var RepositoryService
     */
    private $repositoryService;

    public function __construct(
        AuthenticationService $authenticationService,
        UserPassword $form,
        RepositoryService $repositoryService
    ) {
        $this->authenticationService = $authenticationService;
        $this->form = $form;
        $this->repositoryService = $repositoryService;
    }

    public function indexAction()
    {
        if (!($user = $this->authenticationService->getUser())) {
            throw new UnauthorizedAccessException('You must be logged in.');
        }

        /** @var Request $request */
        $request = $this->getRequest();

        $this->form->bind($user);
        if ($request->isPost()) {
            $this->form->setData($request->getPost()->toArray());
            if ($this->form->isValid()) {
                $this->repositoryService->store($user);
                $vars = array(
                    'valid' => true,
                    'status' => 'success',
                    'text' => /*@translate*/ 'Password successfully changed',
                );
            } else { // form is invalid
                $vars = array(
                    'valid' => false,
                    'status' => 'error',
                    'text' => /*@translate*/ 'Password could not be changed',
                    'errors' => $this->form->getMessages()
                );
            }
        }

        $vars['form'] = $this->form;

        if ($request->isXmlHttpRequest()) {
            return new JsonModel($vars);
        }

        return $vars;
    }

}