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
 * @ODM\Document(collection="organization", repositoryClass="Organizations\Repository\Organization")
 */
class Organization extends BaseEntity implements OrganizationInterface {
    
    /**
     * name
     * 
     * @var Collection \Organizations\Entity\OrganizationName
     * @ODM\EmbedMany(targetDocument="Organizations\Entity\OrganizationName") 
     */
    protected $organizationNames; 
    
    public function __construct()
    {
        //parent::__construct();
        $this->setOrganizationNames(new ArrayCollection());
    }
    
    
   /**
    * @inheritDoc
    */
    public function setOrganizationNames(Collection $organizationNames) 
    {
        $this->organizationNames = $organizationNames;
        return $this;
    }
    
    public function addOrganizationName($organizationName) {
        $name = new OrganizationName($organizationName);
        $this->getOrganizationNames()->add($name);
        return $this;
    }

   /**
    * @inheritDoc
    */
   public function getOrganizationNames() 
   {
       if (!isset($this->organizationNames)) {
           $this->setOrganizationNames(new ArrayCollection());
       }
       return $this->organizationNames;
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