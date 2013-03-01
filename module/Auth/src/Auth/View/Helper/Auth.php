<?php


namespace Auth\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

class Auth extends AbstractHelper
{
    protected $_authService;
    
    public function setService(AuthenticationService $auth)
    {
        $this->_authService = $auth;
        return $this;
    }
    
    public function getService()
    {
        return $this->_authService;
    }
    
    public function __invoke($property=null)
    {
        if (null === $property) {
            return $this;
        }
        
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            return $this->getService()->getIdentity()->$property;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function isLoggedIn()
    {
        return $this->getService()->hasIdentity();
    }
}