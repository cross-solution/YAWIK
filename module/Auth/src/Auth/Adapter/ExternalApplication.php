<?php

namespace Auth\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;
use Auth\Entity\Filter\CredentialFilter;

class ExternalApplication extends AbstractAdapter
{
    
    protected $applicationKey;
    protected $repository;
    protected $applicationKeys = array();
    
    public function __construct($repository, $identity=null, $credential=null, $applicationKey=null)
    {
        $this->repository = $repository;
        $this->setIdentity($identity);
        $this->setCredential($credential);
        $this->setApplicationKey($applicationKey);
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function setApplicationKey($applicationKey)
    {
        $this->applicationKey = $applicationKey;
        return $this;
    }
    
    public function getApplicationKey()
    {
        return $this->applicationKey;
    } 
    
    public function getApplicationIdentifier()
    {
        $keys = $this->getApplicationKeys();
        $ids  = array_flip($keys);
        $key  = $this->getApplicationKey();
        
        return isset($ids[$key]) ? $ids[$key] : null;
    }
    
    public function setApplicationKeys(array $applicationKeys)
    {
        $this->applicationKeys = $applicationKeys;
        return $this;
    }
    
    public function getApplicationKeys()
    {
        return $this->applicationKeys;
    }
    
    public function authenticate()
    {
        if (!in_array($this->getApplicationKey(), $this->getApplicationKeys())) {
            return new Result(Result::FAILURE, $this->getIdentity(), array('Invalid application key'));
        }
        
        $identity    = $this->getIdentity();
        $displayName = $identity . '@' . $this->getApplicationIdentifier();
        $users       = $this->getRepository();
        $user        = $users->findByDisplayName($displayName);
        $filter      = new CredentialFilter();
        $credential  = $this->getCredential();
        
        if (!$user) {
            $user = $users->getUserBuilder()->build(array(
                'displayName' => $displayName,
                'password' => $credential,
            ));
            $users->save($user);
        }
        
        if ($user->credential != $filter->filter($credential)) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, $identity, array('User not known or invalid credential'));
        }
        
        return new Result(Result::SUCCESS, $user);
    }
}