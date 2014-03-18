<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** UniqueGroupName.php */ 
namespace Auth\Form\Validator;

use Zend\Validator\AbstractValidator;
use Auth\Entity\UserInterface;

class UniqueGroupName extends AbstractValidator
{
    const MSG_NOT_UNIQUE = 'msgNotUnique';
    
    protected $user;
    protected $allowName;
    
    protected $messageTemplates = array(
        self::MSG_NOT_UNIQUE => /*@translate*/ 'The group name "%value%" is already in use.',
    );
    
    public function __construct($options=null) {
        if ($options instanceOf UserInterface) {
            $options = array('user' => $options);
        }
        
        parent::__construct($options);
    }
    
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function setAllowName($name)
    {
        $this->allowName = $name;
        return $this;
    }
    
    public function isValid($value)
    {
        if ($value == $this->allowName) {
            return true;
        }
        
        foreach ($this->getUser()->getGroups() as $group) {
            if ($group->getName() == $value) {
                $this->error(self::MSG_NOT_UNIQUE, $value);
                return false;
            }
        }
        return true;
    }
}

