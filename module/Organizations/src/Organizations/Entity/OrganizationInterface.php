<?php
/**
 * YAWIK
 * Organization configuration
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\AddressInterface;
use Core\Entity\SearchableEntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\PermissionsAwareInterface;
use Core\Entity\ModificationDateAwareEntityInterface;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;

/**
 * Interface OrganizationInterface
 * @package Organizations\Entity
 */
interface OrganizationInterface 
    extends EntityInterface, 
            IdentifiableEntityInterface, 
            SearchableEntityInterface,
            ModificationDateAwareEntityInterface,
            PermissionsAwareInterface,
            HydratorAwareInterface
{
    
    
    
   /**
    * Sets the name of the organization
    * 
    * @param OrganizationName organizationName
    * @return OrganizationInterface
    */
   public function setOrganizationName(OrganizationName $organizationNames);

   /**
    * Gets the name of the organization
    *
    * @return OrganizationName
    */
   public function getOrganizationName();
   
   /**
    * Address provides the information about the address or semantic address 
    * of an associated entity.Definition: Based on OAGIS AddressBaseType. 
    * Exception:CountryCode uses the HR country code list.
    * 
    * @param \Core\Entity\AddressInterface
    * @return OrganizationInterface
    */
   public function setAddresses(AddressInterface $addresses);
   
   /**
    * @return AddressInterface
    */
   public function getAddresses();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return self
     */
    public function setDescription($description);
}
