<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Model;

use Core\Model\AbstractModel;

/**
 * The user model
 */
class User extends AbstractModel implements UserInterface
{
    
    /** @var string */
    protected $_email;
    
    /** @var string */ 
    protected $_firstName;
    
    /** @var string */
    protected $_lastName;
    
    /** @var string */
    protected $_displayName;
    
    /** @var array */
    protected $_facebookInfo = array();
    
    /** @var array */
    protected $_linkedInInfo = array();
    
    /** @var array */
    protected $_xingInfo = array();
    
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
            return $name . $this->getLastName();
        }
        return $this->_displayName;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setFacebookInfo(array $info)
    {
        $this->_facebookInfo = $info;
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getFacebookInfo()
    {
        return $this->_facebookInfo;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setLinkedInInfo(array $info)
    {
        $this->_linkedInInfo = $info;
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getLinkedInInfo()
    {
        return $this->_linkedInInfo;
    }
    
    /**
     * {@inheritdoc}
     * @return \Auth\Model\User
     */
    public function setXingInfo(array $info)
    {
        $this->_xingInfo = $info;
        return $this;
    }
    
    /** {@inheritdoc} */
    public function getXingInfo()
    {
        return $this->_xingInfo;
    }
}