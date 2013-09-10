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
class Info extends AbstractEntity implements InfoInterface
{   
    /** @var string */
    protected $_email;
    
    /** @var string */ 
    protected $_firstName;
    
    /** @var string */
    protected $_lastName;
    
    
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
    
    public function getDisplayName()
    {
        if (!$this->lastName) {
            return $this->email;
        }
        return ($this->firstName ? $this->firstName . ' ' : '') . $this->lastName;
    }
    
    
}