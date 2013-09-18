<?php

namespace Auth\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;
use Auth\Entity\Filter\CredentialFilter;

class User extends AbstractAdapter
{
    protected $repository;
    
    public function __construct($repository, $identity=null, $credential=null)
    {
        $this->repository = $repository;
        $this->setIdentity($identity);
        $this->setCredential($credential);
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    
    public function authenticate()
    {
        $identity    = $this->getIdentity();
        $users       = $this->getRepository();
        $user        = $users->findByLogin($identity);
        $filter      = new CredentialFilter();
        $credential  = $this->getCredential();
        
        if (!$user) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $identity, array('User not known or invalid credential'));
        }
        
        if ($user->credential != $filter->filter($credential)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, $identity, array('User not known or invalid credential'));
        }
        
        return new Result(Result::SUCCESS, $user->id);
    }
}