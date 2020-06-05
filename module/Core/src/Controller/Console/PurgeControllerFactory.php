<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Controller\Console;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Controller\Console\PurgeController
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class PurgeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Laminas\Router\RouteMatch $routeMatch */
        $controller = new PurgeController();
        $application = $container->get('Application');
        $routeMatch  = $application->getMvcEvent()->getRouteMatch();

        if ('index' != $routeMatch->getParam('action')) {
            $events = $container->get('Core/EntityEraser/Load/Events');
            $controller->setLoadEvents($events);
        }


        return $controller;
    }
}
