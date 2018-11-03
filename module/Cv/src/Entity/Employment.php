<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Employment extends AbstractIdentifiableEntity
{
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $startDate;
    /**
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $endDate;
    
    /**
     * @ODM\Field(type="boolean")
     * @var bool
     */
    protected $currentIndicator;
    
    /**
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $description;
    
    /**
     *
     * @var string Organisation Name
     * @ODM\Field(type="string")
     */
    protected $organizationName;
    
    public function setStartDate($startDate)
    {
        $this->startDate = (string) $startDate;
        return $this;
    }
    
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getCurrentIndicator()
    {
        return $this->currentIndicator;
    }
    
    public function setCurrentIndicator($currentIndicator)
    {
        $this->currentIndicator = $currentIndicator;
        return $this;
    }
     
    
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    public function setOrganizationName($value)
    {
        $this->organizationName = $value;
        return $this;
    }
    
    public function getOrganizationName()
    {
        return $this->organizationName;
    }
    
    public function setDescription($value)
    {
        $this->description = $value;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
}
