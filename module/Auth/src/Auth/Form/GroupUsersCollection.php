<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** UsersCollection.php */ 
namespace Auth\Form;

use Zend\Form\Element\Collection;
use Core\Form\ViewPartialProviderInterface;
use Auth\Repository\User as UserRepository;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Collection to manage the users assigned to an user group.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class GroupUsersCollection extends Collection implements ViewPartialProviderInterface,
                                                         InputFilterProviderInterface
{
    
    /**
     * Flag wether users are assigned or not upon validation.
     * 
     * @var bool
     */
    protected $errorNoUsers = false;
    
    /**
     * View partial name.
     * @var string
     */
    protected $partial = 'auth/form/userselect';
    
    /**
     * {@inheritDoc}
     * @return GroupsUsersCollection
     * @see \Core\Form\ViewPartialProviderInterface::setViewPartial()
     */
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Form\ViewPartialProviderInterface::getViewPartial()
     */
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    /**
     * Sets the Flag wether no users are assigned to this group or not.
     * 
     * @return \Auth\Form\GroupUsersCollection
     */
    public function setNoUsersError()
    {
        $this->errorNoUsers = true;
        return $this;
    }
    
    /**
     * Returns true, if no users are assigned.
     * 
     * @return boolean
     */
    public function isNoUsersError()
    {
        return $this->errorNoUsers;
    }
    
    /**
     * Initialises the collection.
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setName('users');
        $this->setLabel('Users');
        $this->setAttribute('id', 'users');
        
        
        $this->setTargetElement(array(
            'type' => 'hidden',
            'name' => 'user',
        ));
        
        $this->setCount(0)
             ->setAllowRemove(true)
             ->setAllowAdd(true)
             ->setShouldCreateTemplate(true);
        
        
    }
    
    /**
     * {@inheritDoc}
     * @see \Zend\Form\Element\Collection::extract()
     */
    public function extract()
    {
        if (!is_array($this->object)) {
            return array();
        }
        
        return $this->object;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zend\InputFilter\InputFilterProviderInterface::getInputFilterSpecification()
     */
    public function getInputFilterSpecification()
    {
        $spec = array();
        foreach ($this->getElements() as $element) {
            $name = (string) $element->getName();
            $spec[$name] = array(
                'name' => $name,
            );
        }
        return $spec;
    }
}

