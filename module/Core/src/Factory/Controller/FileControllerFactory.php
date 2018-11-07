<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Core\Factory\Controller;
use Core\Controller\FileController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

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
