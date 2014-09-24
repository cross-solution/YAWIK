<?php

namespace Auth\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;
use Auth\Entity\Filter\CredentialFilter;

/**
 * This class allow to authenticate with a user/password account.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Carsten Bleek     <bleek@cross-solution.de>
 */
class User extends AbstractAdapter
{
    /**
     * User entity repository
     *
     * @var \Core\Repository\RepositoryInterface
     */
    protected $repository;

    /**
     * Initial user.
     *
     * @var null|\Auth\Entity\UserInterface
     */
    protected $defaultUser;

    /**
     * Creates a new user authentication adapter
     *
     * @param \Core\Repository\RepositoryInterface $repository User entity repository
     * @param null|string $identity
     * @param null|string $credential
     */
    public function __construct($repository, $identity=null, $credential=null)
    {
        $this->repository = $repository;
        $this->setIdentity($identity);
        $this->setCredential($credential);
    }

    /**
     * Gets the user repository
     *
     * @return \Core\Repository\RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Sets default user login and password.
     *
     * If no password is provided,
     *
     * @param string $login
     * @param string $password
     * @param string $role (default='recruiter')
     *
     * @return self
     */
    public function setDefaultUser($login, $password, $role=\Auth\Entity\User::ROLE_RECRUITER)
    {
        $this->defaultUser = array($login, $password, $role);
        return $this;
    }

    /**
     * Performs an authentication attempt
     *
     * {@inheritDoc}
     *
     * If the user login does not exists in the database, but the login matches the
     * login of the default user (see {@link setDefaultUser}), the default user will be
     * created and logged in.
     *
     */
    public function authenticate()
    {
        /* @var $users \Auth\Repository\User */
        $identity    = $this->getIdentity();
        $users       = $this->getRepository();
        $user        = $users->findByLogin($identity);
        $filter      = new CredentialFilter();
        $credential  = $this->getCredential();
        
        if (!$user) {
            if (!$this->defaultUser || $identity != $this->defaultUser[0]) {
                return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $identity, array('User not known or invalid credential'));
            }
            $password = $this->defaultUser[1];
            $user = $users->create(array(
                'login' => $identity,
                'password' => $password,
                'role' => $this->defaultUser[2],
            ));
            
            $users->getDocumentManager()->persist($user);
        }
        
        if ($user->getCredential() != $filter->filter($credential)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, $identity, array('User not known or invalid credential'));
        }
        
        return new Result(Result::SUCCESS, $user->getId());
    }
}