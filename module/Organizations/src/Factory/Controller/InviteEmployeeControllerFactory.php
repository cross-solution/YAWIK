<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace Organizations\Factory\Controller;

use Interop\Container\ContainerInterface;
use Organizations\Controller\InviteEmployeeController;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Create new InviteEmployeeController
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Organizations\Factory\Controller
 * @since 0.30
 */
class InviteEmployeeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $orgRepo = $container->get('Core/RepositoryService')->get('Organizations/Organization');

        return new InviteEmployeeController($orgRepo);
    }
}
