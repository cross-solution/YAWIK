<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\Entity;

use GeoJson\GeoJson;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Json\Json;

abstract class AbstractLocation extends AbstractEntity implements LocationInterface
{
    /**
     * Street name
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $streetname;

    /**
     * Street number
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $streetnumber;

    /**
     * city name of a job location
     *
     * @ODM\Field(type="string")
     */
    protected $city;

    /**
     * region of a job location. E.g "Hessen" is a region in germany
     *
     * @ODM\Field(type="string")
     */
    protected $region;

    /**
     * postalcode of a job location.
     *
     * @var String
     * @ODM\Field(type="string")
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
     * @ODM\Field(type="string")
     */
    protected $country;

    public function __construct($attributes = null)
    {
        if (is_string($attributes)) {
            $this->fromString($attributes);
        } else if (is_array($attributes)) {
            $this->fromArray($attributes);
        }
    }
    
    public function __toString()
    {
        $coords = $this->getCoordinates();
        $street = $this->getStreetname();
        $number = $this->getStreetnumber();
        $postalCode = $this->getPostalCode();
        $city = $this->getCity();
        $country = $this->getCountry();
        $region = $this->getRegion();
        
        $str = '';
        if ($street) { $str .= $street; if (!$number) { $str .= ', '; } }
        if ($number) { $str .= ' ' . $number . ', '; }
        if ($postalCode) { $str .= $postalCode . ' '; }
        if ($city) { $str .= $city; }
        if ($region) { $str .= ', ' . $region; }
        if ($country) { $str .= ', ' . $country; }
        if ($coords) {
            $coords = $coords->getCoordinates();
            $str .= ' ( ' . join(', ', $coords) . ' )';
        }
        
        return $str;

    }

    public function toArray()
    {
        $coords = $this->getCoordinates();
        $attributes = [
            'streetname' => $this->getStreetname(),
            'streetnumber' => $this->getStreetnumber(),
            'city' => $this->getCity(),
            'region' => $this->getRegion(),
            'postalcode' => $this->getPostalCode(),
            'country' => $this->getCountry(),
            'coordinates' => $coords
                    ? [
                        'type' => $coords->getType(),
                        'coordinates' => $coords->getCoordinates(),
                      ]
                    : null

        ];
        return $attributes;
    }

    public function toString()
    {
        $attributes = $this->toArray();

        return Json::encode($attributes);
    }

    public function fromArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (!$value) { continue; }

            if ('coordinates' == $key) {
                $class = '\\Geo\\Entity\\Geometry\\' . $value['type'];
                $value = new $class($value['coordinates']);
            }

            $setter = "set$key";
            if (is_callable([$this, $setter])) {
                $this->$setter($value);
            }
        }

        return $this;
    }

    public function fromString($serialized)
    {
        $attributes = Json::decode($serialized, Json::TYPE_ARRAY);

        return $this->fromArray($attributes);
    }

    /**
     * @codeCoverageIgnore
     */
    public function preUpdate()
    {
    }

    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @param GeoJson $coordinates
     *
     * @return $this
     */
    public function setCoordinates(GeoJson $coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * @return String
     */
    public function getPostalCode()
    {
        return $this->postalcode;
    }

    /**
     * @param $postalcode
     *
     * @return $this
     */
    public function setPostalCode($postalcode)
    {
        $this->postalcode = $postalcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetname()
    {
        return $this->streetname;
    }

    /**
     * @param string $streetname
     *
     * @return self
     */
    public function setStreetname($streetname)
    {
        $this->streetname = $streetname;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreetnumber()
    {
        return $this->streetnumber;
    }

    /**
     * @param string $streetnumber
     *
     * @return self
     */
    public function setStreetnumber($streetnumber)
    {
        $this->streetnumber = $streetnumber;

        return $this;
    }



    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param $country
     *
     * @return $this
     */
    public function setCity($country)
    {
        $this->city = $country;
        return $this;
    }

    /**
     * @return String
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param $region
     *
     * @return $this
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }
}
