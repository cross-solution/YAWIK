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

class GroupUsersCollection extends Collection implements ViewPartialProviderInterface,
                                                         InputFilterProviderInterface
{
    protected $errorNoUsers = false;
    
    protected $partial = 'auth/form/userselect';
    
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
    
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    public function setNoUsersError()
    {
        $this->errorNoUsers = true;
        return $this;
    }
    
    public function isNoUsersError()
    {
        return $this->errorNoUsers;
    }
    
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
    
    public function extract()
    {
        if (!is_array($this->object)) {
            return array();
        }
        
        return $this->object;
    }
    
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

