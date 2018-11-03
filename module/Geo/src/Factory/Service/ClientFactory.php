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

use Core\Options\ModuleOptions;
use Geo\Service\Geo;
use Geo\Service\Photon;
use Interop\Container\ContainerInterface;
use Zend\Cache\StorageFactory;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;
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

        /* @var ModuleOptions $coreOptions */
        $coreOptions = $container->get('Core/Options');
        $cacheDir = $coreOptions->getCacheDir().'/geo';
        if (!is_dir(dirname($cacheDir))) {
            throw new ServiceNotCreatedException(
                sprintf('Cache directory "%s" is not writable.', $cacheDir)
            );
        }
        @mkdir($cacheDir, 0755, true);
        $cache = StorageFactory::factory([
            'adapter' => [
                'name' => 'filesystem',
                'options' => [
                    'dirLevel' => 1,
                    'cacheDir' => $cacheDir,
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
}
