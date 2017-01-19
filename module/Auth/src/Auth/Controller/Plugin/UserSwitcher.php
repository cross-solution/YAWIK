<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Auth\Controller\Plugin;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;

/**
 * Plugin to switch logged in user w/o authentication.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class UserSwitcher extends AbstractPlugin
{
    const SESSION_NAMESPACE = "SwitchedUser";

    /**
     * AuthenticationService
     *
     * @var \Zend\Authentication\AuthenticationService
     */
    private $auth;

    /**
     * Creates an instance
     *
     * @param AuthenticationService $auth
     */
    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Switch to or restore an user.
     *
     * If $userId is not null, attempt to switch the user,
     * restore the original user otherwise.
     *
     * @param null|string $userId
     *
     * @return bool
     */
    public function __invoke($userId = null)
    {
        if (null === $userId) {
            return $this->clear();
        }

        return $this->switchUser($userId);
    }

    /**
     * Restores the original user.
     *
     * @return bool
     */
    public function clear()
    {
        $session = $this->getSessionContainer();
        if (!$session->isSwitchedUser) {
            return false;
        }

        $originalUser = $session->originalUser;
        $this->exchangeAuthUser($originalUser);
        /* @var \Zend\Session\Storage\StorageInterface $sessionStorage */
        $sessionStorage = $session->getManager()->getStorage();
        $sessionStorage->clear(self::SESSION_NAMESPACE);

        return true;
    }

    /**
     * Switch to another user.
     *
     * @param string $id user id of the user to switch to.
     *
     * @return bool
     */
    public function switchUser($id)
    {
        $session = $this->getSessionContainer();
        if ($session->isSwitchedUser) {
            return false;
        }

        $session->isSwitchedUser = true;
        $session->originalUser = $this->exchangeAuthUser($id);

        return true;
    }

    /**
     * Gets the session container.
     *
     * @return Container
     */
    private function getSessionContainer()
    {
        return new Container(self::SESSION_NAMESPACE);
    }

    /**
     * Exchanges the authenticated user in AuthenticationService.
     *
     * @param string $id
     *
     * @return string The id of the previously authenticated user.
     */
    private function exchangeAuthUser($id)
    {
        $storage = $this->auth->getStorage();
        $originalUserId = $storage->read();
        $this->auth->clearIdentity();
        $storage->write($id);

        return $originalUserId;
    }
}