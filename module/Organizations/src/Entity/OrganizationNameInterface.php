<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;

/**
 * Interface OrganizationNameInterface
 *
 * @package Organizations\Entity
 */
interface OrganizationNameInterface extends
    EntityInterface,
    IdentifiableEntityInterface
{
    public function getName();

    /**
     * Sets the name of an organization
     *
     * @param string $name
     * @return \Organizations\Entity\OrganizationName
     */
    public function setName($name);

    /**
     * Gets the ranking of an organization
     *
     * @return int $name
     */
    public function getRankingByCompany();

    /**
     * Sets the ranking of an organization
     *
     * @param int $rankingByCompany
     * @return OrganizationName
     */
    public function setRankingByCompany($rankingByCompany);

    /**
     * Sets the id.
     *
     * @param String $id
     * @return OrganizationName
     */
    public function setId($id);

    /**
     * Gets the id of an Organization
     *
     * @return String id of the OrganizationName
     */
    public function getId();

    /**
     * Decrements the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCounterDec();

    /**
     * Increments the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCounterInc();


    /**
     * Decrements the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCompanyCounterDec();

    /**
     * Increments the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCompanyCounterInc();
}
