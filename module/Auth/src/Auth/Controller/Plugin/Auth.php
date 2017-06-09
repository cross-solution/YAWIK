<?php

namespace Auth\Controller\Plugin;

use Auth\Entity\User;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Auth\AuthenticationService as AuthenticationService;
use Zend\Mvc\Controller\PluginManager as ControllerManager;
use Zend\ServiceManager\ServiceManager;

/**
 * @method \Auth\Entity\UserInterface getUser()
 */
class Auth extends AbstractPlugin
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }
    
    /**
     * @param null $property
     *
     * @return $this|bool|null
     */
    public function __invoke($property = null)
    {
        if (null === $property) {
            return $this;
        }
        if (true === $property) {
            return $this->isLoggedIn();
        }
        return $this->get($property);
    }

    /**
     * Checks, if a user is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->authenticationService->hasIdentity();
    }

    /**
     * Checks, if a user is an Admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->authenticationService->getUser()->getRole() == User::ROLE_ADMIN;
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        $auth = $this->authenticationService;
        if (method_exists($auth, $method) && is_callable(array($auth, $method))) {
            return call_user_func_array(array($auth, $method), $params);
        }
        throw new \BadMethodCallException('Unknown method.');
    }

    /**
     * @param $property
     *
     * @return null
     */
    public function get($property)
    {
        $auth = $this->authenticationService;
        if ($auth->hasIdentity()) {
            if (false !== strpos($property, '.')) {
                $value = $auth->getUser();
                foreach (explode('.', $property) as $prop) {
                    $value = $value->$prop;
                }
                return $value;
            }
            return 'id' == $property ? $auth->getIdentity() : $auth->getUser()->{'get' . $property}();
        }
        return null;
    }
    
    /**
     * @param ControllerManager $controllerManager
     * @return \Auth\Controller\Plugin\Auth
     */
    public static function factory(ServiceManager $sm)
    {
    	//$manager = $sm->get('ControllerManager');
        return new static($sm->get('AuthenticationService'));
    }
}
