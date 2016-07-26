<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders;

use Core\ModuleManager\ModuleConfigLoader;
use Zend\ModuleManager\Feature;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Module implements Feature\AutoloaderProviderInterface, Feature\ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        $env    = getenv('APPLICATION_ENV') ?: 'production';
        $config = [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];

        switch ($env) {
            default:
                break;

            case 'production':
                $config['Zend\Loader\ClassMapAutoloader'] = [
                    __DIR__ . '/src/autoload_classmap.php',
                ];
                break;

            case 'testing':
                $config['Zend\Loader\StandardAutoloader']['namespaces'][__NAMESPACE__ . 'Test']
                    = __DIR__ . '/test/' . __NAMESPACE__ . 'Test';
                break;
        }

        return $config;
    }

    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/config');
    }


}