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
 * Factory for \Core\Service\EntityEraser\NameFilter
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class NameFilterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $map    = isset($config['filter_config'][NameFilter::class]['map'])
            ? $config['filter_config'][NameFilter::class]['map']
            : []
        ;

        $service = new NameFilter($map);

        return $service;
    }
}
