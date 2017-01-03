<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Factory\Repository;

use Jobs\Entity\Category;
use Jobs\Repository\DefaultCategoriesBuilder;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for the DefaultCategoriesBuilder.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class DefaultCategoriesBuilderFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('ApplicationConfig');
        $config = $config['module_listener_options'];

        $globalConfigPaths = [];
        foreach ($config['config_glob_paths'] as $path) {
            $globalConfigPaths[] = substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR;
        }

        $moduleConfigPath = '.';
        foreach ($config['module_paths'] as $path) {
            $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            if (file_exists($path . 'Jobs')) {
                $moduleConfigPath = $path . 'Jobs' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
                break;
            }
        }

        $builder = new DefaultCategoriesBuilder($moduleConfigPath, $globalConfigPaths, new Category());

        return $builder;
    }
}