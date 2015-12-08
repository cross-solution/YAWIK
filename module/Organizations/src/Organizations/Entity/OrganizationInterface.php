<?php
/**
 * YAWIK
 * Organization configuration
 *
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Auth\Entity\UserInterface;
use Core\Entity\EntityInterface;
use Core\Entity\AddressInterface;
use Core\Entity\PermissionsResourceInterface;
use Core\Entity\SearchableEntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\PermissionsAwareInterface;
use Core\Entity\ModificationDateAwareEntityInterface;
use Doctrine\Common\Collections\Collection;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;

/**
 * Interface OrganizationInterface
 * @package Organizations\Entity
 */
interface OrganizationInterface extends
    EntityInterface,
    IdentifiableEntityInterface,
    SearchableEntityInterface,
    ModificationDateAwareEntityInterface,
    PermissionsAwareInterface,
    PermissionsResourceInterface,
    HydratorAwareInterface
{


    /**
     * Sets the parent organizations.
     *
     * If this field is set, it means, this organization is a
     * hiring organization (a "customer") and it is NOT allowed
     * to add employees.
     *
     * @param OrganizationInterface $parent
     *
     * @return self
     * @since 0.18
     */
    public function setParent(OrganizationInterface $parent);

    /**
     * Gets the parent organization.
     *
     * @return OrganizationInterface | null
     * @since 0.18
     */
    public function getParent();

    /**
     * Sets contact.
     *
     * @param EntityInterface $contact
     *
     * @return self
     */
    public function setContact(EntityInterface $contact = null);

    /**
     * Gets the contact
     *
     * @return OrganizationContact
     */
    public function getContact();

    /**
     * Checks if the organization is a hiring organization.
     *
     * @internal
     *      Must check for a parent.
     *
     * @return boolean
     * @since 0.18
     */
    public function isHiringOrganization();

    /**
     * Gets the Collection of all hiring organizations.
     *
     * @return Collection
     */
    public function getHiringOrganizations();

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
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return self
     */
    public function setDescription($description);

    /**
     * Sets the employees collection.
     *
     * @internal
     *      NOTE: if a parent is set, this method should throw an exception
     *      or act as a null-op. see {@link setParent()}
     *
     * @param Collection $employees
     *
     * @return self
     * @since 0.18
     */
    public function setEmployees(Collection $employees);

    /**
     * Gets the employees collection,
     *
     * @return Collection
     * @since 0.18
     */
    public function getEmployees();

    /**
     * Gets one employee by user id.
     *
     * @param string|UserInterface $userOrId
     *
     * @return null|EmployeeInterface
     * @since 0.19
     */
    public function getEmployee($userOrId);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param UserInterface $user
     * @return self
     */
    public function setUser(UserInterface $user);

    /**
     * Gets a collection of all jobs the organization is assigned to.
     *
     * @return Collection
     */
    public function getJobs();

    /**
     * Gets default values of an organizations job template
     *
     * @return TemplateInterface
     */
    public function getTemplate();

    /**
     * Sets default values of an organizations job template
     *
     * @return self
     */
    public function setTemplate(TemplateInterface $template);
}
