<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Class OAuth
 * creates and revokes permanent Sessions
 * this instance cannot be shared, but since Sessions are maintained by hybridAuth, there is also no need to
 * @package Auth\Controller\Plugin
 */
class OAuth extends AbstractPlugin
{
    /**
     * @var ContainerInterface
     */
    protected $serviceManager;

    protected $user;

    protected $providerKey;

    protected $adapter;
	
	/**
	 * OAuth constructor.
	 *
	 * @param ContainerInterface $serviceManager
	 */
    public function __construct(ContainerInterface $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
    public function setUser($user)
    {
        if (!empty($this->user)) {
            throw new \RuntimeException('User for oAuth cannot be changed, once the Authentification has been etablished');
        }
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        $user = $this->user;
        // @TODO check on type
        if (empty($user)) {
            $controller = $this->getController();
            $user = $controller->auth()->getUser();
            $this->setUser($user);
        }
        return $user;
    }

    public function getHybridAuth()
    {
        return $this->serviceManager->get('HybridAuth');
    }

    /**
     * @param $providerKey
     * @param null $user
     * @return $this
     */
    public function __invoke($providerKey, $user = null)
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
    public function isAvailable()
    {
        if (!empty($this->adapter)) {
            // adapter is already etablished
            return true;
        }
        $user = $this->getUser();
        $sessionDataStored = $user->getAuthSession($this->providerKey);
        if (empty($sessionDataStored)) {
            // for this user no session has been stored
            return false;
        }
        $hybridAuth = $this->getHybridAuth();
        $hybridAuth->restoreSessionData($sessionDataStored);
        if ($hybridAuth->isConnectedWith($this->providerKey)) {
            return true;
        }
        return false;
    }

    /**
     * everything relevant is happening here, included the interactive registration
     * if the User already has a session, it is retrieved
     */
    public function getAdapter()
    {
        if (empty($this->adapter)) {
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
            $this->getAdapter($this->providerKey)->logout();
        }
        $user->removeSessionData($this->providerKey);
        unset($this->adapter);
        return $this;
    }
	
	/**
	 * @param ContainerInterface $container
	 *
	 * @return static
	 */
    public static function factory(ContainerInterface $container)
    {
        return new static($container);
    }
}
