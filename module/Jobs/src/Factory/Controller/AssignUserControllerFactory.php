<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Factory\Controller;

use Interop\Container\ContainerInterface;
use Jobs\Controller\AssignUserController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class AssignUserControllerFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return AssignUserController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories = $container->get('repositories');
        $repository = $repositories->get('Jobs');
        $controller = new AssignUserController($repository);

        return $controller;
    }
}
