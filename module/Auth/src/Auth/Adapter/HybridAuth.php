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
    protected $_mapper;
    
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
       $user = $this->getMapper()->findByProfileIdentifier($userProfile->identifier);
       if (!$user) {
           $forceSave = true;
           $user = $this->getMapper()->create();
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
           $this->getMapper()->save($user);
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
    public function setMapper(UserMapperInterface $mapper)
    {
        $this->_mapper = $mapper;

        return $this;
    }

    /**
     * Gets the user mapper
     *
     * @return UserMapperInterface
     */
    public function getMapper()
    {
        return $this->_mapper;
    }

    /**
     * Gets a model mapper.
     * 
     * @throws \Auth\Adapter\Exception\InvalidArgumentException
     * @return \Auth\Adapter\ModelMapper\ModelMapperInterface
     */
    protected function _getModelMapper()
    {
        switch ($this->_provider) {
            case 'facebook': return new Facebook(); break;
            case 'linkedin': return new LinkedIn(); break;
            case 'xing': return new Xing(); break;
            default:
                throw new \Auth\Adapter\Exception\InvalidArgumentException('There is no Model mapper for provider: "' . $this->_provider . '"');
                break;
        }
    }
   
   
}
