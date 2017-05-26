<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * ${CARET}
 * 
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class ModuleOptions extends AbstractOptions
{

    /**
     * Used geo location plugin. Possible values:
     *  - photon
     *  - geo
     * 
     * @var string $plugin
     */
    protected $plugin = "photon";

    /**
     * Used geo coder url. Take one of these URLs
     * - http://photon.yawik.org/api
     * - http://api.cross-solution.de/geo
     *
     * @var string
     */
    protected $geoCoderUrl = "http://photon.yawik.org/api";

    /**
     * Country currectly only affects the "geo" plugin. Possible values "DE","CH","FR","AT","IT"
     *
     * @var string
     */
    protected $country = "DE";

    /**
     * @param $plugin
     *
     * @return self
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @param mixed $geoCoderUrl
     *
     * @return self
     */
    public function setGeoCoderUrl($geoCoderUrl)
    {
        $this->geoCoderUrl = $geoCoderUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeoCoderUrl()
    {
        return $this->geoCoderUrl;
    }

    /**
     * @param mixed $country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }
}