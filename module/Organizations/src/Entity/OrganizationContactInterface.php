<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Entity;

use Core\Entity\EntityInterface;

/**
 * Defines the Interface of the contact address of an organization
 */
interface OrganizationContactInterface extends EntityInterface
{

    /**
     * Sets the Building number of an organization address
     *
     * @param string $houseNumber
     * @return OrganizationContact
     */
    public function setHouseNumber($houseNumber = "");

    
    /**
     * Gets the Buildingnumber of an organization address
     *
     * @return string
     */
    public function getHouseNumber();

    /**
     * Sets a postal code of an organization address
     *
     * @param $postalcode
     * @return OrganizationContact
     */
    public function setPostalcode($postalcode);

    /**
     * Gets a postal code of an organization address
     *
     * @return string
     */
    public function getPostalcode();

    /**
     * Sets a city (name) of an organization address
     *
     * @param string $city
     * @return OrganizationContact
     */
    public function setCity($city = "");

    /**
     * Gets a city (name) of an organization address
     *
     * @return string
     */
    public function getCity();

    /**
     * Sets a street name of an organization address
     *
     * @param string $street
     * @return OrganizationContact
     */
    public function setStreet($street = "");

    /**
     * Gets a street name of an organization address
     *
     * @return string
     */
    public function getStreet();

    /**
     * Sets the country of an organization address
     *
     * @param string $country
     * @return OrganizationContact
     */
    public function setCountry($country = "");

    /**
     * Gets the country of an organization address
     *
     * @return string
     */
    public function getCountry();

    /**
     * Sets a phone number of an organization address
     *
     * @param string $phone
     * @return OrganizationContact
     */
    public function setPhone($phone = "");

    /**
     * Gets a phone number name of an organization address
     *
     * @return string
     */
    public function getPhone();

    /**
     * Sets a fax number of an organization address
     *
     * @param string $fax
     * @return OrganizationContact
     */
    public function setFax($fax = "");

    /**
     * Gets a fax number of an organization address
     *
     * @return string
     */
    public function getFax();

    /**
     * Set the website uri
     *
     * @param string $uri
     *
     * @return self
     */
    public function setWebsite($uri);

    /**
     * Get the website uri
     *
     * @return string
     */
    public function getWebsite();
}
