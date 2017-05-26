<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Factory\Service;

use Geo\Service\Geo;
use Geo\Service\Photon;
use Interop\Container\ContainerInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ClientFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Geo\Options\ModuleOptions $geoOptions */
        $geoOptions = $container->get('Geo/Options');
        $plugin = strtolower($geoOptions->getPlugin());
        $url = $geoOptions->getGeoCoderUrl();
        $country = $geoOptions->getCountry();
        $cache = StorageFactory::factory([
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'dirLevel' => 1,
                    'cacheDir' => 'cache/geo',
                    'namespaceSeparator' => '-',
                    'namespace' => $plugin,
                    'dirPermission' => 0755,
                    'filePermission' => 0644,
                ],
            ],
            'plugins' => [
                'serializer',
            ]
        ]);

        switch ($plugin) {
            default:
            case 'photon':
                $client = new Photon($url, $country, $cache);
                break;

            case 'geo':
                $client = new Geo($url, $country, $cache);
                break;
        }

        return $client;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, 'Geo/Client');
    }
}