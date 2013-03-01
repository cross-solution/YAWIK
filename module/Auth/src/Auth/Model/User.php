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
 *
 */
class User extends AbstractModel implements UserInterface
{
    
    protected $_email;
    protected $_firstName;
    protected $_lastName;
    protected $_displayName;
    protected $_facebookInfo = array();
    protected $_linkedInInfo = array();
    protected $_xingInfo = array();
    
    public function setEmail($email) {
        $this->_email = (String) $email;
        return $this;
    }
    
    public function getEmail() {
        return $this->_email;
    }
    
    public function setFirstName($name)
    {
        $this->_firstName = trim((String)$name);
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->_firstName;
    }
    
    public function setLastName($name)
    {
        $this->_lastName = trim((String) $name);
        return $this;
    }
    
    public function getLastName()
    {
        return $this->_lastName;
    }
    
    public function setDisplayName($name)
    {
        $this->_displayName = trim((String) $name);
        return $this;
    }
    
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
    
    public function setFacebookInfo(array $info)
    {
        $this->_facebookInfo = $info;
        return $this;
    }
    
    public function getFacebookInfo()
    {
        return $this->_facebookInfo;
    }
    
    public function setLinkedInInfo(array $info)
    {
        $this->_linkedInInfo = $info;
        return $this;
    }
    
    public function getLinkedInInfo()
    {
        return $this->_linkedInInfo;
    }
    
    public function setXingInfo(array $info)
    {
        $this->_xingInfo = $info;
        return $this;
    }
    
    public function getXingInfo()
    {
        return $this->_xingInfo;
    }
}