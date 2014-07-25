<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */
namespace Auth;

use Zend\Authentication\AuthenticationService as ZendAuthService;
use Zend\Authentication\Adapter\AdapterInterface;
use Core\Repository\RepositoryInterface;
use Auth\Entity\AnonymousUser;

class AuthenticationService extends ZendAuthService {

    protected $user;
    protected $repository;

    public function __construct(RepositoryInterface $repository) {
        $this->setRepository($repository);
    }

    /**
     * @return the $repository
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * @param field_type $repository
     * @return $this
     */
    public function setRepository($repository) {
        $this->repository = $repository;
        return $this;
    }

    public function getUser() {
        if (!$this->user) {
            if ($this->hasIdentity() && ($id = $this->getIdentity())) {
                $user = $this->getRepository()->find($id);
                if (!$user) {
                    throw new \OutOfBoundsException('Unknown user id: ' . $id);
                }
                $this->user = $user;
            } else {
                $this->user = new AnonymousUser();
            }
        }

        return $this->user;
    }

    public function authenticate(AdapterInterface $adapter = null) {
        $this->user = null; // clear user (especially guest user)
        return parent::authenticate($adapter);
    }

}