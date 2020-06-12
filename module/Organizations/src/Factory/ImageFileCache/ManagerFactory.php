<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */
namespace Organizations\Factory\ImageFileCache;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Organizations\ImageFileCache\Manager;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ManagerFactory implements FactoryInterface
{
    /**
     * Create a Manager
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return Manager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Manager($container->get('Organizations/ImageFileCacheOptions'));
    }
}
