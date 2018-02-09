<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Organizations\Factory\Controller;
use Interop\Container\ContainerInterface;
use Organizations\Controller\InviteEmployeeController;
use Zend\ServiceManager\Factory\FactoryInterface;

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
