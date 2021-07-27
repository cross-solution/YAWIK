<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */
namespace Organizations\Factory\ImageFileCache;

use Core\Service\FileManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Organizations\Entity\OrganizationImage;
use Organizations\ImageFileCache\ApplicationListener;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ApplicationListenerFactory implements FactoryInterface
{

    /**
     * Create a ApplicationListener
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ApplicationListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $cacheManager = $container->get('Organizations\ImageFileCache\Manager');
        $fileManager = $container->get(FileManager::class);

        return new ApplicationListener($cacheManager, $fileManager);
    }
}
