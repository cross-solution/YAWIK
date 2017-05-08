<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */

/** Auth adapter */
namespace Auth\Adapter;

use Hybrid_Auth;
use Zend\Authentication\Result;
use Zend\Authentication\Adapter\AdapterInterface;
use Doctrine\MongoDB\GridFSFile;
use Auth\Entity\UserImage;
use Auth\Controller\Plugin\SocialProfiles as SocialProfilePlugin;

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
     * Social profile plugin
     *
     * @var SocialProfilePlugin
     */
    protected $socialProfilePlugin;
    
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
        $user = $this->getRepository()->findByProfileIdentifier($userProfile->identifier, $this->_provider, ['allowDeactivated' => true]);

        if (!$user) {
            $forceSave = true;
            $user = $this->getRepository()->create();
        }
       
       
        $currentInfo = $user->getProfile($this->_provider);
        $socialData = [];
        
        try {
            $socialProfile = $this->socialProfilePlugin->fetch($this->_provider);
            
            if (false !== $socialProfile) {
                $socialData = $socialProfile->getData();
            }
        } catch (\InvalidArgumentException $e) {}
        
        $newInfo = [
            'auth' => (array) $userProfile,
            'data' => $socialData,
        ];
       
        if ($forceSave || $currentInfo != $newInfo) {
            $dm = $this->getRepository()->getDocumentManager();
            $userInfo = $user->getInfo();
            
            if ('' == $userInfo->getEmail()) {
                $userInfo->setEmail($email);
            }
            
            $userInfo->setFirstName($userProfile->firstName);
            $userInfo->setLastName($userProfile->lastName);
            $userInfo->setBirthDay($userProfile->birthDay);
            $userInfo->setBirthMonth($userProfile->birthMonth);
            $userInfo->setBirthYear($userProfile->birthYear);
            $userInfo->setPostalCode($userProfile->zip);
            $userInfo->setCity($userProfile->city);
            $userInfo->setStreet($userProfile->address);
            $userInfo->setPhone($userProfile->phone);
            $userInfo->setGender($userProfile->gender);
            
            // $user->setLogin($email); // this may cause duplicate key exception
            $user->addProfile($this->_provider, $newInfo);

            $dm->persist($user);
            // make sure all ids are generated and user exists in database.
            $dm->flush();

            /*
            * This must be after flush because a newly created user has no id!
            */
            if ($forceSave || (!$userInfo->getImage() && $userProfile->photoURL)) {
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
                    $userInfo->setImage($userImage);
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
	/**
	 * @param SocialProfilePlugin
	 * @return HybridAuth
	 */
	public function setSocialProfilePlugin(SocialProfilePlugin $socialProfilePlugin)
	{
		$this->socialProfilePlugin = $socialProfilePlugin;
		
		return $this;
	}
}
