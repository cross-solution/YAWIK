<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;


/**
 * The user model
 */
class User extends AbstractIdentifiableEntity implements UserInterface
{   
   
    /** @var string */
    protected $login;
    protected $role;
    protected $info;
    
    protected $credential;
    
    /** @var array */
    protected $_profile = array();
    
    /** @var array */
    protected $_settings = array();
    
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setLogin($login)
    {
        $this->login = trim((String) $login);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getLogin()
    {
        return $this->login;
    }
    
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }
    
    public function getRole()
    {
        if (!$this->role) {
            $this->setRole('user');
        }
        return $this->role;
    }
    
    public function getRoleId()
    {
        return $this->getRole();
    }
    
    public function setInfo(EntityInterface $info)
    {
        $this->info = $info;
        return $this;
    }
    
    public function getInfo()
    {
        return $this->info;
    }
    
    public function getCredential()
    {
        return $this->credential;
    }
    
    public function setPassword($password)
    {
        $filter     = new Filter\CredentialFilter();
        $credential = $filter->filter($password); 
        return $this->setCredential($credential);
    }
    
    public function setCredential($credential) 
    {
        $this->credential = $credential;
        return $this;    
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setProfile(array $profile)
    {
        $this->_profile = $profile;
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getProfile()
    {
        return $this->_profile;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setSettings(array $settings)
    {
        $this->_settings = $settings;
        return $this;
    }

    /** {@inheritdoc} */
    public function getSettings()
    {
        return $this->_settings;
    }
   
}