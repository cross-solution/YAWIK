<?php

namespace Auth;

use Zend\Authentication\AuthenticationService as ZendAuthService;
use Core\Repository\RepositoryInterface;

class AuthenticationService extends ZendAuthService
{
    
    protected $user;
    protected $repository;
    
    public function __construct(RepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }
    
    /**
     * @return the $repository
     */
    public function getRepository ()
    {
        return $this->repository;
    }

	/**
     * @param field_type $repository
     */
    public function setRepository ($repository)
    {
        $this->repository = $repository;
        return $this;
    }

	public function getUser()
    {
        if (!$this->user) {
            $id = $this->getIdentity();
            if (null === $id) { return null; }
        
            $user = $this->getRepository()->find($id);
            if (!$user) {
                $this->clearIdentity();
                return null;
            }
            $this->user = $user;
        }
        
        return $this->user;
        
    }
}