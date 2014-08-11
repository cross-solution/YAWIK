<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Core\Entity\EntityInterface;
use Core\Entity\RelationEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Repository\DoctrineMongoODM\Annotation as Cam;
use Doctrine\Common\Collections\Collection;
use Auth\Entity\UserInterface;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Core\Entity\AddressInterface;

/**
 * The job model
 *
 * @ODM\Document(collection="organization", repositoryClass="Organizations\Repository\Organization")
 */
class Organization extends BaseEntity implements OrganizationInterface {
    
   /**
    * @inheritDoc
    */
    public function setOrganizationName($organizationName) 
    {
    }

   /**
    * @inheritDoc
    */
   public function getOrganizationName() 
   {
   }
   
   /**
    * @inheritDoc
    */
   public function setAddress(AddressInterface $address)
   {
   }
   
   /**
    * @inheritDoc
    */
   public function getAddress() 
   {
   }
   
   
   /**
    * @inheritDoc
    */
    public function getSearchableProperties()
    {
    }
    
   /**
    * @inheritDoc
    */
    public function setKeywords(array $keywords)
    {
    }
    
   /**
    * @inheritDoc
    */
    public function clearKeywords()
    {
    }
    
   /**
    * @inheritDoc
    */
    public function getKeywords()
    {
    }
    
   /**
    * @inheritDoc
    */
    public function getPermissions()
    {
    }
    
   /**
    * @inheritDoc
    */
    public function setPermissions(PermissionsInterface $permissions) 
    {
    }
}