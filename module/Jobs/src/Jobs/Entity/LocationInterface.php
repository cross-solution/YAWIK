<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** LocationInterface.php */
namespace Jobs\Entity;

use Core\Entity\EntityInterface;

interface LocationInterface extends EntityInterface
{

    /**
     * Sets the postalcode of a location
     *
     * @param $postalcode
     * @return mixed
     */
    public function setPostalcode($postalcode);

    /**
     * Gets the postalcode of a location
     *
     * @return mixed
     */
    public function getPostalcode();

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
     * @param $point
     * @return mixed
     */
    public function setCoordinates(CoordinatesInterface $coordinates);

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
     * @param $country
     * @return mixed
     */
    public function setRegion($region);

    /**
     * Gets the Region of a location. Eg. "Hessen" is a Region in "germany"
     *
     * @return mixed
     */
    public function getRegion();
}
