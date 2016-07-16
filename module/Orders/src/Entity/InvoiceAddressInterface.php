<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Entity;

use Core\Entity\EntityInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface InvoiceAddressInterface extends EntityInterface
{

    /**
     * Sets the gender of a contact person.
     *
     * @param string $gender
     *
     * @return self
     */
    public function setGender($gender);

    /**
     * Gets the gender of a contact person.
     *
     * @return string
     */
    public function getGender();

    /**
     * Sets the full name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name);

    /**
     * Gets the full name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the company name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setCompany($name);

    /**
     * Gets the company name.
     *
     * @return string
     */
    public function getCompany();

    /**
     * Sets the street name (incl. house number).
     *
     * @param string $street
     *
     * @return self
     */
    public function setStreet($street);

    /**
     * Gets the street name.
     *
     * @return string
     */
    public function getStreet();

    /**
     * Sets the zip code.
     *
     * @param string $zip
     *
     * @return self
     */
    public function setZipCode($zip);

    /**
     * Gets the zip code.
     *
     * @return string
     */
    public function getZipCode();

    /**
     * Sets the city name.
     *
     * @param string $city
     *
     * @return self
     */
    public function setCity($city);

    /**
     * Gets the city name.
     *
     * @return string
     */
    public function getCity();

    /**
     * Sets the region.
     *
     * @param string $region
     *
     * @return self
     */
    public function setRegion($region);

    /**
     * Gets the region.
     *
     * @return string
     */
    public function getRegion();

    /**
     * Sets the country.
     *
     * @param string $country
     *
     * @return self
     */
    public function setCountry($country);

    /**
     * Gets the country.
     *
     * @return string
     */
    public function getCountry();

    /**
     * Sets the value added tax identification number.
     *
     * @param string $vatId
     *
     * @return self
     */
    public function setVatIdNumber($vatId);

    /**
     * Gets the value added tax identification number.
     *
     * @return string
     */
    public function getVatIdNumber();

    /**
     * Sets the email address.
     *
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email);

    /**
     * Gets the email address.
     *
     * @return string
     */
    public function getEmail();
}