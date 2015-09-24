<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth adapter */
namespace Auth\Adapter;

use Hybrid_Auth;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;
use Doctrine\MongoDB\GridFSFile;
use Auth\Entity\UserImage;

/**
 * This class allows to authenticate with HybridAuth
 *
 * HybridAuth adapter for \Zend\Authentication
 *
 * Class HybridAuth
 * @package Auth\Adapter
 */
class HybridAuth implements AdapterInterface
{
    /**
     * HybridAuth instance.
     *
     * @var Hybrid_Auth
     */
    protected $_hybridAuth;

    /**
     * User mapper.
     *
     * @var \Auth\Repository\User
     */
    protected $repository;
    
    /**
     * HybridAuth provider identifier
     *
     * @var string
     */
    protected $_provider;
    
    
    /**
     * Sets the provider identifier used by HybridAuth.
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
     * Gets the provider identifier used by HybridAuth.
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
        /* @var $adapter \Hybrid_Provider_Model */
        $adapter = $hybridAuth->authenticate($this->_provider);

        $userProfile = $adapter->getUserProfile();
        $email = isset($userProfile->emailVerified) && !empty($userProfile->emailVerified)
              ? $userProfile->emailVerified
              : $userProfile->email;
       
       
        $forceSave = false;
        $user = $this->getRepository()->findByProfileIdentifier($userProfile->identifier);

        if (!$user) {
            $forceSave = true;
            $user = $this->getRepository()->create();
        }
       
       
        $currentInfo = $user->getProfile();
        $newInfo = (array) $userProfile;
       
        if ($forceSave || $currentInfo != $newInfo) {
            /*  */

            $dm = $this->getRepository()->getDocumentManager();
            if ( '' == $user->getInfo()->email) $user->getInfo()->email = $email;
            $user->getInfo()->firstName = $userProfile->firstName;
            $user->getInfo()->lastName = $userProfile->lastName;
            $user->getInfo()->birthDay = $userProfile->birthDay;
            $user->getInfo()->birthMonth = $userProfile->birthMonth;
            $user->getInfo()->birthYear = $userProfile->birthYear;
            $user->getInfo()->postalcode = $userProfile->zip;
            $user->getInfo()->city = $userProfile->city;
            $user->getInfo()->street = $userProfile->address;
            $user->getInfo()->phone = $userProfile->phone;
            $user->getInfo()->gender = $userProfile->gender;
            
            $user->setLogin($email);
            $user->setProfile($newInfo);

            $dm->persist($user);
            // make sure all ids are generated and user exists in database.
            $dm->flush();

            /*
            * This must be after flush because a newly created user has no id!
            */
            if ($forceSave || (!$user->getInfo()->image && $userProfile->photoURL)) {
                // get user image
                if ('' != $userProfile->photoURL) {
                    $client = new \Zend\Http\Client($userProfile->photoURL, array('sslverifypeer' => false));
                    $response = $client->send();
                    $file = new GridFSFile();
                    $file->setBytes($response->getBody());

                    $userImage = new UserImage();
                    $userImage->setName($userProfile->lastName.$userProfile->firstName);
                    $userImage->setType($response->getHeaders()->get('Content-Type')->getFieldValue());
                    $userImage->setUser($user);
                    $userImage->setFile($file);
                    $user->getInfo()->setImage($userImage);
                    $dm->persist($userImage);
                    //$this->getRepository()->store($user->info);
                }

                // We have to flush again
                $dm->flush();
            }
        }
       
       
        return new Result(Result::SUCCESS, $user->getId(), array('firstLogin' => $forceSave, 'user' => $user));
        
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
     * Sets the user repository
     *
     * @param $repository
     *
     * @return $this
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Gets the user repository
     *
     * @return \Auth\Repository\User
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
