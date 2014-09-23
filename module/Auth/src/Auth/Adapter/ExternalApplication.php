<?php

namespace Auth\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;
use Auth\Entity\Filter\CredentialFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * This class allows an external application to authenticate via a pre-shared application key.
 *
 * Class ExternalApplication
 * @package Auth\Adapter
 */
class ExternalApplication extends AbstractAdapter implements ServiceLocatorAwareInterface
{
    
    protected $applicationKey;
    protected $repository;
    protected $applicationKeys = array();
    protected $serviceManager;

    /**
     * @param $repository
     * @param null $identity
     * @param null $credential
     * @param null $applicationKey
     */
    public function __construct($repository, $identity=null, $credential=null, $applicationKey=null)
    {
        $this->repository = $repository;
        $this->setIdentity($identity);
        $this->setCredential($credential);
        $this->setApplicationKey($applicationKey);
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceManager) {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceManager;
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param $applicationKey
     * @return $this
     */
    public function setApplicationKey($applicationKey)
    {
        $this->applicationKey = $applicationKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApplicationKey()
    {
        return $this->applicationKey;
    }

    /**
     * @return null
     */
    public function getApplicationIdentifier()
    {
        $keys = $this->getApplicationKeys();
        $ids  = array_flip($keys);
        $key  = $this->getApplicationKey();
        
        return isset($ids[$key]) ? $ids[$key] : null;
    }

    /**
     * @param array $applicationKeys
     * @return $this
     */
    public function setApplicationKeys(array $applicationKeys)
    {
        $this->applicationKeys = $applicationKeys;
        return $this;
    }

    /**
     * @return array
     */
    public function getApplicationKeys()
    {
        return $this->applicationKeys;
    }

    /**
     * @return Result
     */
    public function authenticate()
    {
        if (!in_array($this->getApplicationKey(), $this->getApplicationKeys())) {
            return new Result(Result::FAILURE, $this->getIdentity(), array('Invalid application key'));
        }
                
        $identity      = $this->getIdentity();
        $applicationId = '@' . $this->getApplicationIdentifier();
        $applicationIdIndex = strrpos($identity,$applicationId);
        //$login         = (0 < $applicationIdIndex &&  strlen($identity) - strlen($applicationId) == $applicationIdIndex)?substr($identity, 0, $applicationIdIndex):$identity;
        $login         = $identity;
        $users         = $this->getRepository();
        $user          = $users->findByLogin($login);
        $filter        = new CredentialFilter();
        $credential    = $this->getCredential();
        
        $loginSuccess = False;
        $loginResult = array();
        
        if (0 < $applicationIdIndex &&  strlen($identity) - strlen($applicationId) == $applicationIdIndex) {
            $this->serviceManager->get('Log/Core/Cam')->debug('User ' . $login . ', login with correct suffix: ');
            // the login ends with the applicationID, therefore use the secret key
            // the external login must be the form 'xxxxx@yyyy' where yyyy is the matching suffix to the external application key
            if (isset ($user)) {
                if ($user->secret == $filter->filter($credential)) {
                    $loginSuccess = True;
                }
                else {
                    $loginSuccess = False;
                    $this->serviceManager->get('Log/Core/Cam')->info('User ' . $login . ', secret: ' . $user->secret . ' != loginPassword: ' . $filter->filter($credential) . ' (' . $credential . ')');
                }
            }
            else {
                $user = $users->create(array(
                    'login' => $login,
                    'password' => $credential,
                    'secret' => $filter->filter($credential),
                    'role' => 'recruiter'
                ));
                $users->store($user);
                $loginSuccess = True;
                $loginResult = array('firstLogin' => True);
            }
        }   
        elseif (isset($user)) {
            $this->serviceManager->get('Log/Core/Cam')->debug('User ' . $login . ', login with noncorrect suffix: ');
            if ($user->credential == $filter->filter($credential)) {
                $this->serviceManager->get('Log/Core/Cam')->debug('User ' . $login . ', credentials are equal');
                $loginSuccess = True;
            }
            elseif (!empty($applicationId)) {
                $this->serviceManager->get('Log/Core/Cam')->debug('User ' . $login . ', credentials are not equal');
                // TODO: remove this code as soon as the secret key has been fully established
                // basically this does allow an external login with an applicationIndex match against the User-Password
                // the way it had been used in the start
                if ($user->credential == $filter->filter($credential)) {
                    $this->serviceManager->get('Log/Core/Cam')->debug('User ' . $login . ', credentials2 test');
                    $loginSuccess = True;
                }
            }
        }
        
        if (!$loginSuccess) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, $identity, array('User not known or invalid credential'));
        }
        return new Result(Result::SUCCESS, $user->id, $loginResult);
    }
}