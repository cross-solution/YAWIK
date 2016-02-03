<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Entity;

/**
 * Defines a OrganizationReference entity.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface OrganizationReferenceInterface
{

    /**#@+
     * Reference types.
     *
     * @var string
     */
    const TYPE_NONE = 'none';
    const TYPE_OWNER = 'owner';
    const TYPE_EMPLOYEE = 'employee';
    /**#@-*/

    /**
     * Returns true, if reference is of type TYPE_OWNER
     *
     * @return boolean
     */
    public function isOwner();

    /**
     * Returns true, if the reference is of type TYPE_EMPLOYEE.
     *
     * @return boolean
     */
    public function isEmployee();

    /**
     * Returns true, if the user is associated with an organization.
     *
     * @return boolean
     */
    public function hasAssociation();

    /**
     * Gets the referenced organization.
     *
     * @return null|OrganizationInterface
     */
    public function getOrganization();
}
