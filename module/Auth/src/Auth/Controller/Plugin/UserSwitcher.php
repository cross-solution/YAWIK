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
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class UserSwitcher extends AbstractPlugin
{
    const SESSION_NAMESPACE = "SwitchedUser";

    /**
     *
     *
     * @var \Zend\Authentication\AuthenticationService
     */
    private $auth;

    public function __construct(AuthenticationService $auth)
    {
        $this->auth = $auth;
    }

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

    public function __invoke($userId = null)
    {
        if (null === $userId) {
            return $this->clear();
        }

        return $this->switchUser($userId);
    }

    private function getSessionContainer()
    {
        return new Container(self::SESSION_NAMESPACE);
    }

    private function exchangeAuthUser($id)
    {
        $storage = $this->auth->getStorage();
        $originalUserId = $storage->read();
        $this->auth->clearIdentity();
        $storage->write($id);

        return $originalUserId;
    }
}