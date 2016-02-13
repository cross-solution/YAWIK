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
use Zend\Http\Client;

class Photon extends AbstractPlugin
{
    /**
     * @param string $par Query Parameter
     * @param string $geoCoderUrl Url of the geo location service
     * @param string $land language
     *
     * @return array|mixed|string
     */
    public function __invoke($par, $geoCoderUrl, $lang)
    {
        $client = new Client($geoCoderUrl);
        $client->setMethod('GET');

        $osmTags = [
            'tourism','aeroway','railway', 'amenity', 'historic', 'tunnel', 'mountain_pass',
            'leisure', 'natural', 'bridge', 'waterway'
        ];

        $osmTags = array_map(function($i) { return urlencode('!' . $i); }, $osmTags);

        $uri = sprintf(
            '%s?q=%s&lang=%s&osm_tag=%s',
            $geoCoderUrl,
            urlencode($par),
            $lang,
            implode('&osm_tag=', $osmTags)
        );

        $client->setUri($uri);


        $response = $client->send();
        $result = $response->getBody();
        $result = json_decode($result);
        $result = $result->features;
        $r=[];
        foreach ($result as $key => $val) {
            $row=['name' => (property_exists($val->properties, 'name') ? $val->properties->name:''),
                  'postcode' => (property_exists($val->properties, 'postcode') ? $val->properties->postcode:''),
                  'city' =>(property_exists($val->properties, 'city') ? $val->properties->city:''),
                  'street' => (property_exists($val->properties, 'street') ? $val->properties->street : ''),
                  'state' => (property_exists($val->properties, 'state') ? $val->properties->state : ''),
                  'country' => (property_exists($val->properties, 'country') ? $val->properties->country : ''),
                  'coordinates' => implode(":", $val->geometry->coordinates),
                  'osm_key' => (property_exists($val->properties, 'osm_key') ? $val->properties->osm_key : ''),
                  'osm_value' => (property_exists($val->properties, 'osm_value') ? $val->properties->osm_value : ''),
                  'osm_id' => (property_exists($val->properties, 'osm_id') ? $val->properties->osm_id : ''),
                  'data' => json_encode($val),
            ];
            $r[]=$row;
        }
        return $r;
    }
}