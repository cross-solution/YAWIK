<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Geo\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Geo extends AbstractPlugin
{
    /**
     * @param string $par Query Parameter
     * @param string $geoCoderUrl Url of the geo location service
     * @param string $land language
     *
     * @return array|mixed|string
     */
    public function __invoke($par, $geoCoderUrl, $land)
    {

        $client = new \Zend\Http\Client($geoCoderUrl);
        $client->setMethod('GET');
        // more countries 'country' => 'DE,CH,AT'
        // with countryCode 'zoom' => 2
        $plz = 0 < (int) $par?1:0;
        $client->setParameterGet(array('q' => $par, 'country' => 'DE', 'plz' => $plz, 'zoom' => 1));
        $response = $client->send();
        $result = $response->getBody();
        $result = json_decode($result);
        $result = (array) $result->result;
        return $result;
    }
}