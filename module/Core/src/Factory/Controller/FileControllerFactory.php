<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace Core\Factory\Controller;
use Core\Controller\FileController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Creates new FileController object
 *
 * @package Core\Factory\Controller
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.30
 */
class FileControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories = $container->get('repositories');
        $coreFileEvents = $container->get('Core/File/Events');

        return new FileController($repositories,$coreFileEvents);
    }
}
