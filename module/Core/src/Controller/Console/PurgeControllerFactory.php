<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Controller\Console;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

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
        /* @var \Zend\Router\RouteMatch $routeMatch */
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
