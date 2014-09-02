<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableEntity;
//use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Name of an Organization.
 *
 * @ODM\Document(collection="organizations.names", repositoryClass="Organizations\Repository\OrganizationName")
 */
class OrganizationName extends AbstractIdentifiableEntity implements OrganizationNameInterface {
    
    /**
     * name of the Organization
     * 
     * @var string
     * @ODM\String
     */
    protected $name;
    
    /**
     * Flag, if the nane is used
     * 
     * @var int
     * @ODM\Int
     */
    protected $rankingByCompany;
    
    /**
     * Overall numbers of use
     * 
     * @var int
     * @ODM\Int
     */
    protected $ranking;
    
    public function __construct($name = Null) 
    {
        $this->ranking = 0;
        $this->rankingByCompany = 0;
        if (!empty($name)) {
            $this->name = $name;
        }
        //parent::__construct();
    }
    
    /**
     * @return String $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return String $name
     */
    public function getRankingByCompany()
    {
        return $this->rankingByCompany;
    }

    /**
     * @param string $rankingByCompany
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
     */
    public function setId($id) 
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * Gets the id.
     * 
     * @return String id of the OrganizationName
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return OrganizationName
     */
    public function refCounterDec()
    {
        $this->ranking -= 1;
        return $this;
    }

    /**
     * @return OrganizationName
     */
    public function refCounterInc()
    {
        $this->ranking += 1;
        return $this;
    }

    /**
     * @return OrganizationName
     */
    public function refCompanyCounterDec()
    {
        $this->rankingByCompany -= 1;
        return $this;
    }

    /**
     * @return OrganizationName
     */
    public function refCompanyCounterInc()
    {
        $this->rankingByCompany += 1;
        return $this;
    }
    
}