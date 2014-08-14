<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Repository\DoctrineMongoODM\Annotation as Cam;
use Doctrine\Common\Collections\Collection;
use Core\Entity\AddressInterface;
use Core\Entity\Permissions;
use Core\Entity\PermissionsInterface;
use Core\Entity\Collection\ArrayCollection;

/**
 * The job model
 *
 * @ODM\Document(collection="organizations", repositoryClass="Organizations\Repository\Organization")
 */
class Organization extends BaseEntity implements OrganizationInterface {
    
    /**
     * name
     * 
     * @var \Organizations\Entity\OrganizationName
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationName", simple=true, cascade="persist")
     */
    protected $organizationName; 
    
   /**
    * @inheritDoc
    */
    public function setOrganizationName($organizationName) 
    {
        $this->organizationName = $organizationName;
        return $this;
    }
    
 
   /**
    * @inheritDoc
    */
   public function getOrganizationName() 
   {
       return $this->organizationName;
   }
   
   /**
    * @inheritDoc
    */
   public function setAddresses(AddressInterface $addresses)
   {
   }
   
   /**
    * @inheritDoc
    */
   public function getAddresses() 
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