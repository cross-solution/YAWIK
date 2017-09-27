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

use Acl\Controller\Plugin\Acl;
use Auth\Entity\UserInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Container;
use Zend\Stdlib\ArrayUtils;

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
     * @var \Auth\AuthenticationService
     */
    private $auth;

    /**
     * The session container.
     *
     * @var Container
     */
    private $sessionContainer;

    /**
     *
     *
     * @var Acl
     */
    private $aclPlugin;

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
    public function __invoke($userId = null, array $params = [])
    {
        if (null === $userId) {
            return $this->clear();
        }

        return $this->switchUser($userId, $params);
    }

    /**
     * @param \Acl\Controller\Plugin\Acl $aclPlugin
     *
     * @return self
     */
    public function setAclPlugin($aclPlugin)
    {
        $this->aclPlugin = $aclPlugin;

        return $this;
    }

    /**
     * @return \Acl\Controller\Plugin\Acl
     */
    public function getAclPlugin()
    {
        return $this->aclPlugin;
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

        $oldSession = unserialize($session->session);
        $ref        = $session->ref;
        $_SESSION = $oldSession;

        return $ref ? $ref : true;
    }

    /**
     * Switch to another user.
     *
     * @param string|UserInterface $id user id of the user to switch to.
     * @param array $params Additional parameters to store in the session container.
     *
     * @return bool
     */
    public function switchUser($id, array $params = [])
    {
        if ($id instanceOf UserInterface) {
            $id = $id->getId();
        }

        $oldSession = serialize($_SESSION);

        $session = $this->getSessionContainer();
        if ($session->isSwitchedUser) {
            return false;
        }

        $session->session      = $oldSession;
        $session->isSwitchedUser = true;
        $session->originalUser = $this->exchangeAuthUser($id);
        $session->params       = $params;
        $session->ref          = isset($params['ref']) ? $params['ref'] : $this->getController()->getRequest()->getRequestUri();

        return true;
    }

    /**
     * Is the current user a switched one?
     *
     * @return bool
     */
    public function isSwitchedUser()
    {
        $session = $this->getSessionContainer();

        return isset($session->isSwitchedUser) && $session->isSwitchedUser;
    }

    /**
     * Set additional params.
     *
     * @param array $params
     * @param bool  $merge Merges with existing params.
     *
     * @return self
     */
    public function setSessionParams(array $params, $merge = false)
    {
        $session = $this->getSessionContainer();

        if (isset($session->params) && $merge) {
            $params = ArrayUtils::merge($session->params, $params);
        }

        $session->params = $params;

        return $this;
    }

    /**
     * Get additional params
     *
     * @return array
     */
    public function getSessionParams()
    {
        $session = $this->getSessionContainer();

        return isset($session->params) ? $session->params : [];
    }

    /**
     * Get a param.
     *
     * @param string $key
     * @param mixed $default Value to return if param $key is not set.
     *
     * @return null
     */
    public function getSessionParam($key, $default = null)
    {
        $params = $this->getSessionParams();

        return array_key_exists($key, $params) ? $params[$key] : $default;
    }

    /**
     * Set a param.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return UserSwitcher
     */
    public function setSessionParam($key, $value)
    {
        return $this->setSessionParams([$key => $value], true);
    }

    /**
     * Gets the session container.
     *
     * @return Container
     */
    private function getSessionContainer()
    {
        if (!$this->sessionContainer) {
            $this->sessionContainer = new Container(self::SESSION_NAMESPACE);
        }

        return $this->sessionContainer;
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

        if ($acl = $this->getAclPlugin()) {
            $acl->setUser($this->auth->getUser());
        }

        return $originalUserId;
    }
}
