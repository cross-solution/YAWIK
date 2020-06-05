<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Service\EntityEraser;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \Core\Service\EntityEraser\DefaultEntityLoaderListener
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class DefaultEntityLoaderListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $nameFilter = $container->get('FilterManager')->get(NameFilter::class);
        $service = new DefaultEntityLoaderListener($nameFilter);

        return $service;
    }
}
