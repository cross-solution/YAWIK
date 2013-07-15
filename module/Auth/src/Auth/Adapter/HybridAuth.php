<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth adapter */
namespace Auth\Adapter;

use Hybrid_Auth;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;
use Auth\Mapper\UserMapperInterface;


/**
 * Hybridauth adapter for \Zend\Authentication
 */
class HybridAuth implements AdapterInterface
{
    /**
     * Hybridauth instance.
     * 
     * @var Hybrid_Auth
     */
    protected $_hybridAuth;

    /**
     * User mapper.
     * 
     * @var \Auth\Mapper\MongoDb\UserMapper
     */
    protected $repository;
    
    /**
     * Hybridauth provider identifier
     *  
     * @var string
     */
    protected $_provider;
    
    
    /**
     * Sets the provider identifier used by Hybridauth.
     * 
     * @param string $provider
     * @return HybridAuth
     */
    public function setProvider($provider)
    {
        $this->_provider = $provider;
        return $this;
    }
    
    /**
     * Gets the provider identifier used by Hybridauth.
     * 
     * @return string|null
     */
    public function getProvider()
    {
        return $this->_provider;
    }
    
    /**
     * {@inheritdoc}
     * 
     * 
     * @see \Zend\Authentication\Adapter\AdapterInterface::authenticate()
     */
    public function authenticate()
    {
        
       $hybridAuth = $this->getHybridAuth();
       $adapter = $hybridAuth->authenticate($this->_provider);
       
       $userProfile = $adapter->getUserProfile();
       $email = isset($userProfile->emailVerified) && !empty($userProfile->emailVerified)
              ? $userProfile->emailVerified
              : $userProfile->email;
       
       
       $forceSave = false;
       $user = $this->getRepository()->findByProfileIdentifier($userProfile->identifier);
       if (!$user) {
           $forceSave = true;
           $user = $this->getRepository()->getUserBuilder()->build();
       }
       
       
       $currentInfo = $user->profile;
       $newInfo = (array) $userProfile; 
       
       if ($forceSave || $currentInfo != $newInfo) {
           $user->setData(array(
               'email' => $email,
               'firstName' => $userProfile->firstName,
               'lastName' => $userProfile->lastName,
               'displayName' => $userProfile->displayName,
               'profile' => $newInfo
           ));
           $this->getRepository()->save($user);
       }
       
       
       return new Result(Result::SUCCESS, $user);
        
    }

    /**
     * Get the Hybrid_Auth object
     *
     * @return Hybrid_Auth
     */
    public function getHybridAuth()
    {
        return $this->_hybridAuth;
    }

    /**
     * Set the Hybrid_Auth object
     *
     * @param  Hybrid_Auth    $hybridAuth
     * @return HybridAuth
     */
    public function setHybridAuth(Hybrid_Auth $hybridAuth)
    {
        $this->_hybridAuth = $hybridAuth;

        return $this;
    }

    
    /**
     * Sets the user mapper
     *
     * @param  UserMapperInterface $mapper
     * @return HybridAuth
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Gets the user mapper
     *
     * @return UserMapperInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

   
   
   
}
