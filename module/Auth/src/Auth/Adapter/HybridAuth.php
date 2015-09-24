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

use Doctrine\ODM\MongoDB\DocumentManager;
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
       
       
        $currentInfo = $user->profile;
        $newInfo = (array) $userProfile;
       
        if ($forceSave || $currentInfo != $newInfo) {
            /*  */

            $dm = $this->getRepository()->getDocumentManager();
            if ( '' == $user->info->email) $user->info->email = $email;
            $user->info->firstName = $userProfile->firstName;
            $user->info->lastName = $userProfile->lastName;
            $user->info->birthDay = $userProfile->birthDay;
            $user->info->birthMonth = $userProfile->birthMonth;
            $user->info->birthYear = $userProfile->birthYear;
            $user->info->postalcode = $userProfile->zip;
            $user->info->city = $userProfile->city;
            $user->info->street = $userProfile->address;
            $user->info->phone = $userProfile->phone;
            $user->info->gender = $userProfile->gender;
            
            $user->login =  $email;
           

            $user->profile = $newInfo;
            $dm->persist($user);
            // make sure all ids are generated and user exists in database.
            $dm->flush();

            /*
            * This must be after flush because a newly created user has no id!
            */
            if ($forceSave || (!$user->info->image && $userProfile->photoURL)) {
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
                    $user->info->setImage($userImage);
                    $dm->persist($userImage);
                    //$this->getRepository()->store($user->info);
                }

                // We have to flush again
                $dm->flush();
            }
        }
       
       
        return new Result(Result::SUCCESS, $user->id, array('firstLogin' => $forceSave, 'user' => $user));
        
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
