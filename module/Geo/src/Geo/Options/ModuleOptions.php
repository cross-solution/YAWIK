<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * ${CARET}
 * 
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @todo write test 
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
}