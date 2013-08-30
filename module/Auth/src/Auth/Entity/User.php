<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Entity;

use Core\Entity\AbstractEntity;

/**
 * The user model
 */
class User extends AbstractEntity implements UserInterface
{   
    /** @var string */
    protected $_email;
    
    /** @var string */ 
    protected $_firstName;
    
    /** @var string */
    protected $_lastName;
    
    /** @var string */
    protected $_displayName;
    
    protected $credential;
    
    /** @var array */
    protected $_profile = array();
    
    /** @var array */
    protected $_settings = array();
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setEmail($email) {
        $this->_email = (String) $email;
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getEmail() {
        return $this->_email;
    }

    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setFirstName($name)
    {
        $this->_firstName = trim((String)$name);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getFirstName()
    {
        return $this->_firstName;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setLastName($name)
    {
        $this->_lastName = trim((String) $name);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getLastName()
    {
        return $this->_lastName;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setDisplayName($name)
    {
        $this->_displayName = trim((String) $name);
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getDisplayName()
    {
        if (!$this->_displayName) {
            $name = $this->getFirstName();
            if ($name) {
                $name .= ' ';
            }
            $this->setDisplayName($name . $this->getLastName());
        }
        return $this->_displayName;
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