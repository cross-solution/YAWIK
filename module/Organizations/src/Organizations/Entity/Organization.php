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
use Core\Entity\EntityInterface;

/**
 * The job model
 *
 * @ODM\Document(collection="organizations", repositoryClass="Organizations\Repository\Organization")
 */
class Organization extends BaseEntity implements OrganizationInterface {
    
    
    const postConstruct = 'postRepositoryConstruct';
    
    /**
     * externalId
     * 
     * @var string
     * @ODM\String
     */
    protected $externalId; 
    
    /**
     * OrganizationName
     * 
     * @var \Organizations\Entity\OrganizationName
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationName", simple=true, cascade="persist")
     */
    protected $organizationName; 
    
    /**
     * 
     * @var \Organizations\Entity\OrganizationImage
     * @ODM\ReferenceOne(targetDocument="\Organizations\Entity\OrganizationImage", cascade={"persist","update","remove"}, orphanRemoval=true, simple=true, nullable=true) 
     */
    protected $image; 
    
   /**
    * @inheritDoc
    */
    public function setExternalId($externalId) 
    {
        $this->externalId = $externalId;
        return $this;
    }
    
   /**
    * @inheritDoc
    */
    public function getExternalId() 
    {
        return $this->externalId;
    }
    
   /**
    * @inheritDoc
    */
    public function setOrganizationName(OrganizationName $organizationName) 
    {
        if (isset($this->organizationName)) {
            $this->organizationName->refCounterDec()->refCompanyCounterDec();
        }
        $this->organizationName = $organizationName;
        $this->organizationName->refCounterInc()->refCompanyCounterInc();
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
        // @TODO: set Permissions
    }
    
    public function setImage(EntityInterface $image = null)
    {
        $this->image = $image;
        return $this;
    }
    
    public function getImage()
    {
        return $this->image;
    }
    
}


