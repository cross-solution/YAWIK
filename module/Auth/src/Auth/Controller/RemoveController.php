<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Auth\Controller;

use Auth\Repository\User;
use Zend\Mvc\Controller\AbstractActionController;
use Auth\Dependency\Manager as Dependencies;
use Auth\AuthenticationService;

/**
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>r
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.27
 */
class RemoveController extends AbstractActionController
{
    /**
     * @var Dependencies
     */
    protected $dependencies;
    
    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     *
     *
     * @var User
     */
    protected $userRepository;

    /**
     * @param Dependencies $dependencies
     * @param AuthenticationService $auth
     * @param \Auth\Repository\User $userRepository
     */
    public function __construct(Dependencies $dependencies, AuthenticationService $auth, User $userRepository)
    {
        $this->dependencies = $dependencies;
        $this->auth = $auth;
        $this->userRepository = $userRepository;
    }

    public function indexAction()
    {
        $user = $this->auth->getUser();
        $error = false;
        
        if ($this->params()->fromPost('confirm'))
        {
            if ($this->dependencies->removeItems($user)) {
                $this->auth->clearIdentity();
                $this->userRepository->remove($user);
                return $this->redirect()->toRoute('lang');
            } else {
                $error = true;
            }
        }
        
        return [
            'lists' => $this->dependencies->getLists(),
            'user' => $user,
            'limit' => 20,
            'error' => $error,
        ];
    }
}
