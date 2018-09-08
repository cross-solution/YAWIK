<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Group.php */
namespace Auth\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\PermissionsInterface;
use Core\Entity\Permissions;
use Doctrine\Common\Collections\Collection;
use Core\Entity\PermissionsResourceInterface;
use Core\Entity\AbstractIdentifiableEntity;

/**
 * User Group Entity.
 *
 * This entity allows to define a group of users, which then can be used
 * to assign permissions to other entities for this group of users at once.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @ODM\Document(collection="users.groups")
 */
class Group extends AbstractIdentifiableEntity implements
    GroupInterface,
    PermissionsResourceInterface
{
    /**
     *
     * @var UserInterface
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="groups", storeAs="id")
     */
    protected $owner;
    
    /**
     * Name of the Group.
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $name;
    
    /**
     * Array of user ids that belongs to this group
     *
     * @var array
     * @ODM\Field(type="collection")
     */
    protected $users;
    
    public function __construct($name = null, UserInterface $owner = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }
        if (null !== $owner) {
            $this->setOwner($owner);
        }
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    public function setOwner(UserInterface $user)
    {
        $this->owner = $user;
        return $this;
    }
    
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
    
    
    public function getPermissionsResourceId()
    {
        return 'group:' . $this->getId();
    }
    
    /**
     * {inheritDoc}
     * @param array $users
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
    
    public function getPermissionsUserIds()
    {
        return $this->getUsers();
    }
}
