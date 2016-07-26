<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/** */
namespace Auth\View\Helper;

use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\AbstractHelper;
use Auth\Entity\User;

/**
 * View helper to access authentication service and the
 * authenticated user (and its properties).
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
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
     * Proxies to the AuthenticationService.
     *
     * @param $method
     * @param $params
     *
     * @return mixed
     * @throws \DomainException
     */
    public function __call($method, $params)
    {
        $callback = array($this->getService(), $method);
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        throw new \DomainException(
            sprintf(
                'Could not proxy "%s" to Authentication Service. Method does not exist',
                $method
            )
        );
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
     * Sets the authentication service
     *
     * @param AuthenticationService $auth
     *
     * @return self
     */
    public function setService(AuthenticationService $auth)
    {
        $this->_authService = $auth;

        return $this;
    }

    /**
     * Returns itself or a property value of the authenticated user.
     *
     * Returns null, if no user is authenticated or the property does not exists.
     *
     * @param string $property
     *
     * @return self|NULL
     */
    public function __invoke($property = null)
    {
        if (null === $property) {
            return $this;
        }

        try {
            /* @var $service \Auth\AuthenticationService */
            $service = $this->getService();
            $user    = $service->getUser();
            $value   = $user->$property;

            return $value;
        } catch (\OutOfBoundsException $e) {
            return null;
        }
    }

    /**
     * Checks if an user is authenticated.
     *
     * Proxies to \Zend\AuthenticationService\AuthenticationService::hasIdentity()
     *
     * @return bool
     * @use getService()
     */
    public function isLoggedIn()
    {
        return $this->getService()->hasIdentity();
    }

    /**
     * Checks, if a user is an Admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getService()->getUser()->getRole() == User::ROLE_ADMIN;
    }
}
