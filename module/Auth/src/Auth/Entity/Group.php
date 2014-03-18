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
 * 
 * @ODM\EmbeddedDocument
 */
class Group extends AbstractEntity implements GroupInterface
{
    /**
     * 
     * @var string
     * @ODM\String
     */
    protected $name;
    
    /**
     * 
     * @var array
     * @ODM\Collection
     */
    protected $users;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }
    
    public function setUsers($users)
    {
        $this->users = $users;
        return $this;
    }
    
    public function getUsers()
    {
        return $this->users;
    }
}

