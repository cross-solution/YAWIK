<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

/** UniqueApplyIdFactory.php */
namespace Jobs\Form\Validator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UniqueApplyIdFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories = $container->get('repositories');
        $jobs         = $repositories->get('Jobs/Job');
        $validator    = new UniqueApplyId($jobs);

        return $validator;
    }
}
