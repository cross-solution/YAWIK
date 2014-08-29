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
 * Name 
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
     * @return the $name
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName ($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return the $name
     */
    public function getRankingByCompany ()
    {
        return $this->rankingByCompany;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setRankingByCompany ($used)
    {
        $this->rankingByCompany = $rankingByCompany;
        return $this;
    }
    
    /**
     * Sets the id.
     * 
     * @param mixed $id
     */
    public function setId($id) 
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * Gets the id.
     * 
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function refCounterDec()
    {
        $this->ranking -= 1;
        return $this;
    }
    
    public function refCounterInc()
    {
        $this->ranking += 1;
        return $this;
    }
    
    public function refCompanyCounterDec()
    {
        $this->rankingByCompany -= 1;
        return $this;
    }
    
    public function refCompanyCounterInc()
    {
        $this->rankingByCompany += 1;
        return $this;
    }
    
}