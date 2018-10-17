<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\AbstractIdentifiableHydratorAwareEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Defines the contact address of an organization
 *
 * @ODM\EmbeddedDocument
 */
class OrganizationContact extends AbstractIdentifiableHydratorAwareEntity implements OrganizationContactInterface
{
   
    
    /**
     * BuildingNumber of an organization address
     *
     * @var string
     * @ODM\Field(type="string") */
    protected $houseNumber;
    
    /**
     * Postalcode of an organization address
     *
     * @var string
     * @ODM\Field(type="string") */
    protected $postalcode;

    /**
     * Cityname of an organization address
     *
     * @var string
     * @ODM\Field(type="string") */
    protected $city;
    
    /**
     * Streetname of an organization address
     *
     * @var string
     * @ODM\Field(type="string") */
    protected $street;

    /**
     * Phone number of an organization address
     *
     * @var string
     * @ODM\Field(type="string") */
    protected $phone;

    /**
     * country of an organization address
     *
     * @var string
     * @ODM\Field(type="string") */
    protected $country;

    /**
     * Fax number of an organization address
     *
     * @var string
     * @ODM\Field(type="string") */
    protected $fax;

    /**
     * The website of the organization.
     *
     * @ODM\Field
     * @var string
     */
    private $website;

    /**
     * Sets the Buildingnumber of an organization address
     *
     * @param string $houseNumber
     * @return OrganizationContact
     */
    public function setHouseNumber($houseNumber = "")
    {
        $this->houseNumber=$houseNumber;
        return $this;
    }
    
    /**
     * Gets the Buildingnumber of an organization address
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Sets a postal code of an organization address
     *
     * @param $postalcode
     * @return OrganizationContact
     */
    public function setPostalcode($postalcode)
    {
        $this->postalcode = (String) $postalcode;
        return $this;
    }

    /**
     * Gets a postal code of an organization address
     *
     * @return string
     */
    public function getPostalcode()
    {
        return $this->postalcode;
    }
    
    /**
     * Sets a city (name) of an organization address
     *
     * @param string $city
     * @return OrganizationContact
     */
    public function setCity($city = "")
    {
        $this->city = (String) $city;
        return $this;
    }

    /**
     * Gets a city (name) of an organization address
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
    
    /**
     * Sets a street name of an organization address
     *
     * @param string $street
     * @return OrganizationContact
     */
    public function setStreet($street = "")
    {
        $this->street=$street;
        return $this;
    }

    /**
     * Gets a street name of an organization address
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Sets teh country of an organization address
     *
     * @param string $country
     * @return OrganizationContact
     */
    public function setCountry($country = "")
    {
        $this->country=$country;
        return $this;
    }

    /**
     * Gets the country of an organization address
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }



    /**
     * Sets a phone number of an organization address
     *
     * @param string $phone
     *
     * @return OrganizationContact
     */
    public function setPhone($phone = "")
    {
        $this->phone=$phone;
        return $this;
    }

    /**
     * Gets a phone number name of an organization address
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets a fax number of an organization address
     *
     * @param string $fax
     *
     * @return OrganizationContact
     */
    public function setFax($fax = "")
    {
        $this->fax=$fax;
        return $this;
    }

    /**
     * Gets a fax number of an organization address
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    public function getWebsite()
    {
        return $this->website;
    }
}
