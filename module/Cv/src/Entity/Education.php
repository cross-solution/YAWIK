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
class Education extends AbstractIdentifiableEntity
{

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $startDate;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $endDate;

    /**
     * @var bool
     * @ODM\Field(type="boolean")
     */
    protected $currentIndicator;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $competencyName;

    /**
     * @ODM\Field(type="string")
     */
    protected $organizationName;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $description;

    /**
     * needed for europass
     * @ODM\Field(type="string")
     */
    protected $nationalClassification;
    
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $country;
    
    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $city;
    
    
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
    
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    /**
     * marks the education as ongoing
     *
     * @param bool $currentIndicator
     * @return \Cv\Entity\Education
     */
    public function setCurrentIndicator($currentIndicator)
    {
        $this->currentIndicator=$currentIndicator;
        return $this;
    }
    
    /**
     * indicates that the education is ongoing
     *
     * @return bool
     */
    public function getCurrentIndicator()
    {
        return $this->currentIndicator;
    }

    public function setCompetencyName($competencyName)
    {
        $this->competencyName = $competencyName;
        return $this;
    }
    
    public function getCompetencyName()
    {
        return $this->competencyName;
    }
    
    /**
     * @return $organizationName
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * @param field_type $organizationName
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;
        return $this;
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
    
    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return Education
     */
    public function setCountry($country)
    {
        $this->country = $country;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return Education
     */
    public function setCity($city)
    {
        $this->city = $city;
        
        return $this;
    }
}
