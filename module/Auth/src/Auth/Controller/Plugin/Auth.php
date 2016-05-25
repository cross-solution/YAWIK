<?php

namespace Auth\Controller\Plugin;

use Auth\Entity\User;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;

/**
 * @method \Auth\Entity\UserInterface getUser()
 */
class Auth extends AbstractPlugin
{
    protected $auth;
    
    public function setAuthenticationService(AuthenticationService $auth)
    {
        $this->auth = $auth;
        return $this;
    }
    
    public function getAuthenticationService()
    {
        if (!$this->auth) {
            $services = $this->getController()->getServiceLocator();
            $auth     = $services->get('AuthenticationService');
            $this->setAuthenticationService($auth);
        }
        return $this->auth;
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
        return $this->getAuthenticationService()->hasIdentity();
    }

    /**
     * Checks, if a user is an Admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getAuthenticationService()->getUser()->getRole() == User::ROLE_ADMIN;
    }

    /**
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        $auth = $this->getAuthenticationService();
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
        $auth = $this->getAuthenticationService();
        if ($auth->hasIdentity()) {
            if (false !== strpos($property, '.')) {
                $value = $auth->getUser();
                foreach (explode('.', $property) as $prop) {
                    $value = $value->$prop;
                }
                return $value;
            }
            return 'id' == $property ? $auth->getIdentity() : $auth->getUser()->$property;
        }
        return null;
    }
}
