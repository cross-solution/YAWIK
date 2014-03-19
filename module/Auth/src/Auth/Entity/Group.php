<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Group.php */ 
namespace Auth\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\PermissionsInterface;
use Core\Entity\Permissions;
use Doctrine\Common\Collections\Collection;

/**
 * User Group Entity.
 * 
 * This entity allows to define a group of users, which then can be used
 * to assign permissions to other entities for this group of users at once.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\EmbeddedDocument
 */
class Group extends AbstractEntity implements GroupInterface
{
    /**
     * Name of the Group.
     * 
     * @var string
     * @ODM\String
     */
    protected $name;
    
    /**
     * Array of user ids that belongs to this group
     * 
     * @var array
     * @ODM\Collection
     */
    protected $users;
    
    /**
     * {@inheritDoc}
     * @see \Auth\Entity\GroupInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * {@inheritDoc}
     * @return Group
     * @see \Auth\Entity\GroupInterface::setName()
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }
    
    /**
     * {inheritDoc}
     * @return Group
     * @see \Auth\Entity\GroupInterface::setUsers()
     */
    public function setUsers(array $users)
    {
        $this->users = $users;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Auth\Entity\GroupInterface::getUsers()
     */
    public function getUsers()
    {
        return $this->users;
    }
}

