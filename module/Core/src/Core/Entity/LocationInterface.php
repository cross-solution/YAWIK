<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** LocationInterface.php */
namespace Core\Entity;

use GeoJson\GeoJson;
use Jobs\Entity\CoordinatesInterface;

interface LocationInterface extends EntityInterface
{

    /**
     * Sets the Postal Code of a location
     *
     * @param   string $postalCode
     * @return mixed
     */
    public function setPostalCode($postalCode);

    /**
     * Gets the Postal Code of a location
     *
     * @return mixed
     */
    public function getPostalCode();

    /**
     * Gets the coordinates lon/lat of a location
     *
     * @return CoordinatesInterface
     *
     */
    public function getCoordinates();

    /**
     * Sets the coordinates lon/lat of a location
     *
     * @param GeoJson $coordinates
     * @internal param $point
     */
    public function setCoordinates(GeoJson $coordinates);

    /**
     * Sets the city name of a Location
     *
     * @param $city
     * @return mixed
     */
    public function setCity($city);

    /**
     * Gets the city name of a location
     *
     * @return mixed
     */
    public function getCity();

    /**
     * Sets the country of a location
     *
     * @param $country
     * @return mixed
     */
    public function setCountry($country);

    /**
     * Gets the country of a location
     *
     * @return mixed
     */
    public function getCountry();

    /**
     * Sets the region of a location. Eg. "Hessen" is a region in "Germany"
     *
     * @param $region
     * @return mixed
     */
    public function setRegion($region);

    /**
     * Gets the Region of a location. Eg. "Hessen" is a Region in "germany"
     *
     * @return mixed
     */
    public function getRegion();

    /**
     * Sets the Streetname of a location
     *
     * @param $street
     * @return mixed
     */
    public function setStreetname($street);

    /**
     * Gets the streetname of a location
     *
     * @return mixed
     */
    public function getStreetname();

    /**
     * Sets the Streetnumber of a location
     *
     * @param $number
     * @return mixed
     */
    public function setStreetnumber($number);

    /**
     * Gets the streetnumber of a location
     *
     * @return mixeed
     */
    public function getStreetnumber();
}
