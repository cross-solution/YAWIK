<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

namespace Geo\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Geo extends AbstractPlugin
{
    /* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function __invoke($par)
    { 
        $config = $this->getController()->getServiceLocator()->get('config');
        if (empty($config['cross_geoapi_url'])) {
             throw new \InvalidArgumentException('Now Service-Adress for Geo-Service available');
        }
        $client = new \Zend\Http\Client($config['cross_geoapi_url']);
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
