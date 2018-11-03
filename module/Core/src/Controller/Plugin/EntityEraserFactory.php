<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */

/** */

namespace Core\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Controller\Plugin\EntityEraser
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 */
class EntityEraserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new EntityEraser(
            $container->get('Core/EntityEraser/Dependencies/Events'),
            $container->get('Core/EntityEraser/Load/Events'),
            $container->get('repositories')
        );
    }
}
