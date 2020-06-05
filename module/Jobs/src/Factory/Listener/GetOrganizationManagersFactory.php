<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Factory\Listener;

use Interop\Container\ContainerInterface;
use Jobs\Listener\GetOrganizationManagers;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class GetOrganizationManagersFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories = $container->get('repositories');
        $repository   = $repositories->get('Organizations');
        $listener     = new GetOrganizationManagers($repository);

        return $listener;
    }
}
