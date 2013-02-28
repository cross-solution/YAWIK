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

class HybridAuth implements AdapterInterface
{
    /**
     * @var Hybrid_Auth
     */
    protected $_hybridAuth;

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

       var_dump($userProfile);
       return new Result(Result::SUCCESS, $userProfile);
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
    public function setMapper(UserProviderInterface $mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * get mapper
     *
     * @return UserProviderInterface
     */
    public function getMapper()
    {
        if (!$this->mapper instanceof UserProviderInterface) {
            $this->setMapper($this->getServiceLocator()->get('ScnSocialAuth-UserProviderMapper'));
        }

        return $this->mapper;
    }

    /**
     * set zfcUserMapper
     *
     * @param  UserMapperInterface $zfcUserMapper
     * @return HybridAuth
     */
    public function setZfcUserMapper(UserMapperInterface $zfcUserMapper)
    {
        $this->zfcUserMapper = $zfcUserMapper;

        return $this;
    }

    /**
     * get zfcUserMapper
     *
     * @return UserMapperInterface
     */
    public function getZfcUserMapper()
    {
        if (!$this->zfcUserMapper instanceof UserMapperInterface) {
            $this->setZfcUserMapper($this->getServiceLocator()->get('zfcuser_user_mapper'));
        }

        return $this->zfcUserMapper;
    }

    /**
     * Utility function to instantiate a fresh local user object
     *
     * @return mixed
     */
    protected function instantiateLocalUser()
    {
        $userModelClass = $this->getZfcUserOptions()->getUserEntityClass();

        return new $userModelClass;
    }

    // Provider specific methods

    protected function facebookToLocalUser($userProfile)
    {
        if (!isset($userProfile->emailVerified)) {
            throw new Exception\RuntimeException(
                'Please verify your email with Facebook before attempting login',
                Result::FAILURE_CREDENTIAL_INVALID
            );
        }
        $mapper = $this->getZfcUserMapper();
        if (false != ($localUser = $mapper->findByEmail($userProfile->emailVerified))) {
            return $localUser;
        }
        $localUser = $this->instantiateLocalUser();
        $localUser->setEmail($userProfile->emailVerified)
            ->setDisplayName($userProfile->displayName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'facebook', $userProfile);

        return $localUser;
    }

    protected function foursquareToLocalUser($userProfile)
    {
        if (!isset($userProfile->emailVerified)) {
            throw new Exception\RuntimeException(
                'Please verify your email with Foursquare before attempting login',
                Result::FAILURE_CREDENTIAL_INVALID
            );
        }
        $mapper = $this->getZfcUserMapper();
        if (false != ($localUser = $mapper->findByEmail($userProfile->emailVerified))) {
            return $localUser;
        }
        $localUser = $this->instantiateLocalUser();
        $localUser->setEmail($userProfile->emailVerified)
            ->setDisplayName($userProfile->displayName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'foursquare', $userProfile);

        return $localUser;
    }

    protected function googleToLocalUser($userProfile)
    {
        if (!isset($userProfile->emailVerified)) {
            throw new Exception\RuntimeException(
                'Please verify your email with Google before attempting login',
                Result::FAILURE_CREDENTIAL_INVALID
            );
        }
        $mapper = $this->getZfcUserMapper();
        if (false != ($localUser = $mapper->findByEmail($userProfile->emailVerified))) {
            return $localUser;
        }
        $localUser = $this->instantiateLocalUser();
        $localUser->setEmail($userProfile->emailVerified)
            ->setDisplayName($userProfile->displayName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'google', $userProfile);

        return $localUser;
    }

    protected function linkedInToLocalUser($userProfile)
    {
        $localUser = $this->instantiateLocalUser();
        $localUser->setDisplayName($userProfile->displayName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'linkedIn', $userProfile);

        return $localUser;
    }

    protected function twitterToLocalUser($userProfile)
    {
        $localUser = $this->instantiateLocalUser();
        $localUser->setUsername($userProfile->displayName)
            ->setDisplayName($userProfile->firstName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'twitter', $userProfile);

        return $localUser;
    }

    protected function yahooToLocalUser($userProfile)
    {
        $localUser = $this->instantiateLocalUser();
        $localUser->setDisplayName($userProfile->displayName)
            ->setPassword(__FUNCTION__);
        $result = $this->insert($localUser, 'yahoo', $userProfile);

        return $localUser;
    }

    protected function githubToLocalUser($userProfile)
    {
        $localUser = $this->instantiateLocalUser();
        $localUser->setDisplayName($userProfile->displayName)
                  ->setPassword(__FUNCTION__)
                  ->setEmail($userProfile->email);

        $this->getEventManager()->trigger(__FUNCTION__, $localUser, array('userProfile' => $userProfile));

        $result = $this->insert($localUser, 'github', $userProfile);

        return $localUser;
    }

   
   
}
