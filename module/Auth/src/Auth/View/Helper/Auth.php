<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth view helper */
namespace Auth\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

/**
 * View helper to access authentication service and the 
 * authenticated user (and its properties).
 * 
 */
class Auth extends AbstractHelper
{
    /**
     * AuthenticationService instance
     * 
     * @var AuthenticationService
     */
    protected $_authService;
    
    /**
     * Sets the authentication service
     * 
     * @param AuthenticationService $auth
     * @return \Auth\View\Helper\Auth
     */
    public function setService(AuthenticationService $auth)
    {
        $this->_authService = $auth;
        return $this;
    }
    
    /**
     * Gets the authentication service
     * 
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getService()
    {
        return $this->_authService;
    }
    
    /**
     * Entry point.
     * 
     * Returns itself if called without arguments.
     * Returns a property value of the authenticated user or null, if
     * no user is authenticated or the property does not exists.
     * 
     * @param string $property
     * @return \Auth\View\Helper\Auth|NULL
     */
    public function __invoke($property=null)
    {
        if (null === $property) {
            return $this;
        }
        
//         if (!$this->isLoggedIn()) {
//             return null;
//         }
        
        try {
            return $this->getService()->getUser()->$property;
        } catch (\Core\Model\Exception\OutOfBoundsException $e) {
            return null;
        }
    }
    
   
    /**
     * Checks if an user is authenticated.
     * 
     *  Mirrors to \Zend\AuthenticationService\AuthenticationService::hasIdentity()
     *  
     * @return boolean
     * @use getService()
     */
    public function isLoggedIn()
    {
        return $this->getService()->hasIdentity();
    }
}