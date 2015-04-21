<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Controller\Plugin;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class OAuth
 * creates and revokes permanent Sessions
 * this instance cannot be shared, but since Sessions are maintained by hybridAuth, there is also no need to
 * @package Auth\Controller\Plugin
 */
class OAuth extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    protected $user;

    protected $providerKey;

    protected $adapter;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setUser($user) {
        if (!empty($this->user)) {
            throw new \RuntimeException('User for oAuth cannot be changed, once the Authentification has been etablished');
        }
        $this->user = $user;
        return $this;
    }

    public function getUser() {
        $user = $this->user;
        // @TODO check on type
        if (empty($user)) {
            $controller = $this->getController();
            $user = $controller->auth()->getUser();
            $this->setUser($user);
        }
        return $user;
    }

    public function getHybridAuth() {
        $services = $this->getServiceLocator()->getServiceLocator();
        return $services->get('HybridAuth');
    }

    /**
     * @param $providerKey
     * @param null $user
     * @return $this
     */
    public function __invoke($providerKey, $user = Null)
    {
        if (!empty($user)) {
            $this->setUser($user);
        }
        $this->providerKey = $providerKey;
        return $this;
    }

    /**
     * for backend there is only one possibility to get a connection,
     * and that is by stored Session
     * @return bool
     */
    public function isAvailable() {
        if (!empty($this->adapter))
        {
            // adapter is already etablished
            return True;
        }
        $user = $this->getUser();
        $sessionDataStored = $user->getAuthSession($this->providerKey);
        if (empty($sessionDataStored)) {
            // for this user no session has been stored
            return False;
        }
        $hybridAuth = $this->getHybridAuth();
        $hybridAuth->restoreSessionData($sessionDataStored);
        if ($hybridAuth->isConnectedWith($this->providerKey)) {
            return True;
        }
        return False;
    }

    /**
     * everything relevant is happening here, included the interactive registration
     * if the User already has a session, it is retrieved
     */
    public function getAdapter()
    {
        if (empty($this->adapter))
        {
            $user = $this->getUser();
            $sessionDataStored = $user->getAuthSession($this->providerKey);
            $hybridAuth = $this->getHybridAuth();
            if (!empty($sessionDataStored)) {
                $hybridAuth->restoreSessionData($sessionDataStored);
            }
            $adapter = $hybridAuth->authenticate($this->providerKey);
            $sessionData    = $hybridAuth->getSessionData();
            if ($sessionData != $sessionDataStored) {
                $user->updateAuthSession($this->providerKey, $sessionData);
            }
            $this->adapter = $adapter;
        }
        return $this->adapter;
    }

    /**
     * logout and clears the stored Session,
     */
    public function sweepProvider()
    {
        $user = $this->getUser();
        $hybridAuth = $this->getHybridAuth();
        // first test, if there is a connection at all
        // that prevents an authentification just for to logout
        if ($hybridAuth->isConnectedWith($this->providerKey)) {
            $this->getAdapter( $this->providerKey)->logout();
        }
        $user->removeSessionData($this->providerKey);
        unset($this->adapter);
        return $this;
    }
}