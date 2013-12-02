<?php

namespace Auth\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;

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
    
    public function __invoke($property=null)
    {
        if (null === $property) {
            return $this;
        }
        if (true === $property) {
            return $this->isLoggedIn();
        }
        return $this->get($property);
    }
    
    public function isLoggedIn()
    {
        return $this->getAuthenticationService()->hasIdentity();
    }
    
    public function __call($method, $params)
    {
        $auth = $this->getAuthenticationService();
        if (method_exists($auth, $method) && is_callable(array($auth, $method))) {
            return call_user_func_array(array($auth, $method), $params);
        }
        throw new \BadMethodCallException('Unknown method.');
    }
    
    public function get($property)
    {
        $auth = $this->getAuthenticationService();
        if ($auth->hasIdentity()) {
            return 'id' == $property ? $auth->getIdentity() : $auth->getUser()->$property;
        }
        return null;
        
    }
}
