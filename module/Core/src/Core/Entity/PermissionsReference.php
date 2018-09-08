<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PermissionsReference.php */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class PermissionsReference
{
    
    /**
     *
     * @var unknown
     * @ODM\Field(type="string")
     */
    protected $permission;
    
    /**
     * @ODM\Field(type="collection")
     */
    protected $userIds;
    
    /**
     * @ODM\ReferenceOne(discriminatorField="_entity", discriminatorMap={
     *      "user"="\Auth\Entity\User",
     *      "group"="\Auth\Entity\Group"
     *  })
     */
    protected $resource;
    
    public function __construct(PermissionsResourceInterface $resource, $permission)
    {
        $this->resource   = $resource;
        $this->userIds    = $resource->getPermissionsUserIds();
        $this->permission = $permission;
    }

    public function getUserIds()
    {

    }
}