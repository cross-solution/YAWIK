<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Adapter;

use Hybrid_Auth;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;
use Core\Mapper\MapperInterface;
use Auth\Adapter\ModelMapper\Facebook;
use Auth\Adapter\ModelMapper\LinkedIn;
use Auth\Adapter\ModelMapper\Xing;

class HybridAuth implements AdapterInterface
{
    /**
     * @var Hybrid_Auth
     */
    protected $_hybridAuth;

    protected $_mapper;
    /**
     * 
     * @var string
     */
    protected $_provider;
    
    
    public function setProvider($provider)
    {
        $this->_provider = $provider;
    }
    
    public function authenticate()
    {
        
       $hybridAuth = $this->getHybridAuth();
       $adapter = $hybridAuth->authenticate($this->_provider);
       $userProfile = $adapter->getUserProfile();
       $email = isset($userProfile->emailVerified) && !empty($userProfile->emailVerified)
              ? $userProfile->emailVerified
              : $userProfile->email;
       
       $user = $this->getMapper()->findByEmail($email);
       if (!$user) {
           $user = $this->getMapper()->create();
       }
       $this->_getModelMapper()->map($userProfile, $user);
       $this->getMapper()->save($user);
       
       
       return new Result(Result::SUCCESS, $user);
        if (!$userProfile) {
            $authEvent->setCode(Result::FAILURE_IDENTITY_NOT_FOUND)
              ->setMessages(array('A record with the supplied identity could not be found.'));
            $this->setSatisfied(false);

            return false;
        }

        $localUserProvider = $this->getMapper()->findUserByProviderId($userProfile->identifier, $provider);
        if (false == $localUserProvider) {
            $method = $provider.'ToLocalUser';
            if (method_exists($this, $method)) {
                try {
                    $localUser = $this->$method($userProfile);
                } catch (Exception\RuntimeException $ex) {
                    $authEvent->setCode($ex->getCode())
                        ->setMessages(array($ex->getMessage()))
                        ->stopPropagation();
                    $this->setSatisfied(false);

                    return false;
                }
            } else {
                $localUser = $this->instantiateLocalUser();
                $localUser->setDisplayName($userProfile->displayName)
                          ->setPassword($provider);
                if ($userProfile->emailVerified) $localUser->setEmail($userProfile->emailVerified);
                $result = $this->insert($localUser, 'other', $userProfile);
            }
            $localUserProvider = clone($this->getMapper()->getEntityPrototype());
            $localUserProvider->setUserId($localUser->getId())
                ->setProviderId($userProfile->identifier)
                ->setProvider($provider);
            $this->getMapper()->insert($localUserProvider);
        }

        $authEvent->setIdentity($localUserProvider->getUserId());

        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $authEvent->getIdentity();
        $this->getStorage()->write($storage);
        $authEvent->setCode(Result::SUCCESS)
          ->setMessages(array('Authentication successful.'))
          ->stopPropagation();
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
     * set mapper
     *
     * @param  UserProviderInterface $mapper
     * @return HybridAuth
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->_mapper = $mapper;

        return $this;
    }

    /**
     * get mapper
     *
     * @return UserProviderInterface
     */
    public function getMapper()
    {
        return $this->_mapper;
    }

    protected function _getModelMapper()
    {
        switch ($this->_provider) {
            case 'facebook': return new Facebook(); break;
            case 'linkedin': return new LinkedIn(); break;
            case 'xing': return new Xing(); break;
            default:
                throw new \RuntimeException('Could not load ModelMapper for provider "' . $this->_provider . '"');
                break;
        }
    }
   
   
}
