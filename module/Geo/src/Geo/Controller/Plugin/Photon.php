<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Geo\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Photon extends AbstractPlugin
{
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function __invoke($par)
    {
        $config = $this->getController()->getServiceLocator()->get('config');
        $lang   = $this->getController()->getEvent()->getRouteMatch()->getParam('lang', 'en');

        if (empty($config['geocoder_photon_url'])) {
             throw new \InvalidArgumentException('Now Service-Adress for Geo-Service available');
        }
        $client = new \Zend\Http\Client($config['geocoder_photon_url']);
        $client->setMethod('GET');

        $osmTags = [
            'tourism','aeroway','railway', 'amenity', 'historic', 'tunnel', 'mountain_pass',
            'leisure', 'natural', 'bridge', 'waterway'
        ];

        $osmTags = array_map(function($i) { return urlencode('!' . $i); }, $osmTags);

        $uri = sprintf(
            '%s?q=%s&lang=%s&osm_tag=%s',
            $config['geocoder_photon_url'],
            urlencode($par),
            $lang,
            implode('&osm_tag=', $osmTags)
        );

        $client->setUri($uri);


        $response = $client->send();
        $result = $response->getBody();
        $result = json_decode($result);
        $result = $result->features;
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
            $r[] = $row;
        }
        return $r;
    }
}
