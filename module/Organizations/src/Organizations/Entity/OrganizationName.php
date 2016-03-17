<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableEntity;
//use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Name of an Organization. Organization names can be used by applicants in the work history or by Companies
 * in job positions as a reference to the hiring Organization.
 *
 * @ODM\Document(collection="organizations.names", repositoryClass="Organizations\Repository\OrganizationName")
 */
class OrganizationName extends AbstractIdentifiableEntity implements OrganizationNameInterface
{
    
    /**
     * name of the Organization
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $name;
    
    /**
     * Reference counter. If the name is used as an hiring organization name, the counter is incremented.
     *
     * @var int
     * @ODM\Field(type="int")
     */
    protected $rankingByCompany;
    
    /**
     * Overall numbers of use
     *
     * @var int
     * @ODM\Field(type="int")
     */
    protected $ranking;

    /**
     * @param string|null $name Name of the organization
     */
    public function __construct($name = null)
    {
        $this->ranking = 0;
        $this->rankingByCompany = 0;
        if (!empty($name)) {
            $this->name = $name;
        }
        //parent::__construct();
    }
    
    /**
     * Gets the name of an organization
     *
     * @return String $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of an organization
     *
     * @param string $name
     * @return \Organizations\Entity\OrganizationName
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Gets the ranking of an organization
     *
     * @return int $name
     */
    public function getRankingByCompany()
    {
        return $this->rankingByCompany;
    }

    /**
     * Sets the ranking of an organization
     *
     * @param int $rankingByCompany
     * @return OrganizationName
     */
    public function setRankingByCompany($rankingByCompany)
    {
        $this->rankingByCompany = $rankingByCompany;
        return $this;
    }
    
    /**
     * Sets the id.
     *
     * @param String $id
     * @return OrganizationName
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * Gets the id of an Organization
     *
     * @return String id of the OrganizationName
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Decrements the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCounterDec()
    {
        $this->ranking -= 1;
        return $this;
    }

    /**
     * Increments the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCounterInc()
    {
        $this->ranking += 1;
        return $this;
    }

    /**
     * Decrements the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCompanyCounterDec()
    {
        $this->rankingByCompany -= 1;
        return $this;
    }

    /**
     * Increments the reference counter of an organization
     *
     * @return OrganizationName
     */
    public function refCompanyCounterInc()
    {
        $this->rankingByCompany += 1;
        return $this;
    }
}
