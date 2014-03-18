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

/**
 * Validator for uniqueness check of group names.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class UniqueGroupName extends AbstractValidator
{
    /**
     * Messages
     * @var string
     */
    const MSG_NOT_UNIQUE = 'msgNotUnique';
    
    /**
     * The current logged in user.
     * 
     * @var UserInterface
     */
    protected $user;
    
    /**
     * This name will be allowed, even if the value equals it.
     * @var string
     */
    protected $allowName;
    
    /**
     * {@inheritDoc}
     */
    protected $messageTemplates = array(
        self::MSG_NOT_UNIQUE => /*@translate*/ 'The group name "%value%" is already in use.',
    );
    
    /**
     * Creates an instance.
     * 
     * @param UserInterface|array|null $options
     */
    public function __construct($options=null) {
        if ($options instanceOf UserInterface) {
            $options = array('user' => $options);
        }
        
        parent::__construct($options);
    }
    
    /**
     * Sets the user the group should belong to.
     * 
     * @param UserInterface $user
     * @return \Auth\Form\Validator\UniqueGroupName
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }
    
    /**
     * Gets the user.
     * 
     * @return \Auth\Entity\UserInterface
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * Sets the name which will be allowed.
     * 
     * @param string $name
     * @return \Auth\Form\Validator\UniqueGroupName
     */
    public function setAllowName($name)
    {
        $this->allowName = $name;
        return $this;
    }
    
    /**
     * Returns true, if the given value is unique among the groups of the user.
     * 
     * Also returns true, if the given value equals the {@link $allowName}.
     * 
     * @return bool
     * @see \Zend\Validator\ValidatorInterface::isValid()
     */
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

