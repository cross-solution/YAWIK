<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Location.php */
namespace Jobs\Entity;

use Core\Entity\AbstractEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use GeoJson\GeoJson;

/**
 * Location of a job position
 *
 * @ODM\EmbeddedDocument
 * @ODM\Index(keys={"coordinates"="2dsphere"})
 *
 */
class Location extends AbstractEntity implements LocationInterface
{
    /**
     * city name of a job location
     *
     * @ODM\String
     */
    protected $city;

    /**
     * region of a job location. E.g "Hessen" is a region in germany
     *
     * @ODM\String
     */
    protected $region;

    /**
     * postalcode of a job location.
     *
     * @var String
     * @ODM\String
     */
    protected $postalcode;

    /**
     * coordinates of a job location.
     *
     * @var GeoJson
     * @ODM\EmbedOne(discriminatorField="_entity")
     */
    protected $coordinates;

    /**
     * Country of a job location
     * @var String
     *
     * @ODM\String
     */
    protected $country;
    
    public function __construct()
    {
    }
    
    public function preUpdate()
    {
    }

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    public function setCoordinates(GeoJson $coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    public function getPostalcode()
    {
        return $this->postalcode;
    }

    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;
        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($country)
    {
        $this->city = $country;
        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }
}