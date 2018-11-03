<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Factory\Form;

use Interop\Container\ContainerInterface;
use Organizations\Entity\EmployeePermissions;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Hydrator\Strategy\ClosureStrategy;
use Organizations\Entity\EmployeePermissionsInterface as Perms;
use Organizations\Form\EmployeeFieldset;

/**
 * Factory for an EmployeeFieldset
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo extract hydrating strategies
 * @since 0.18
 */
class EmployeeFieldsetFactory implements FactoryInterface
{

    /**
     * Create a EmployeeFieldset fieldset
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return EmployeeFieldset
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fieldset = new EmployeeFieldset();

        $hydrator = new \Zend\Hydrator\ClassMethods(false); //new EntityHydrator();
        $repositories = $container->get('repositories');
        $users        = $repositories->get('Auth/User'); /* @var $users \Auth\Repository\User */

        /* todo: WRITE own Hydrator strategy class */
        $strategy = new ClosureStrategy(
            function ($object) use ($users) {
                if (is_string($object)) {
                    return $users->find($object);
                }
                return $object;
            },
            function ($data) use ($users) {
                if (is_string($data)) {
                    $data = $users->find($data);
                }
                return $data;
            }
        );

        /* todo: write own strategy class */
        $permStrategy = new ClosureStrategy(
        // extract
            function ($object) {
                /* @var $object \Organizations\Entity\EmployeePermissionsInterface */
                $values = array();
                foreach (array(
                                Perms::JOBS_VIEW, Perms::JOBS_CHANGE, PERMS::JOBS_CREATE,
                                Perms::APPLICATIONS_VIEW, Perms::APPLICATIONS_CHANGE)
                            as $perm) {
                    if ($object->isAllowed($perm)) {
                        $values[] = $perm;
                    }
                }

                return $values;
            },
            function ($data) {
                $permissions = array_reduce(
                    $data,
                    function ($c, $i) {
                        return $c | $i;
                    },
                    0
                );
                return new EmployeePermissions($permissions);
            }
        );

        $hydrator->addStrategy('user', $strategy);
        $hydrator->addStrategy('permissions', $permStrategy);
        $fieldset->setHydrator($hydrator);
        $fieldset->setObject(new \Organizations\Entity\Employee());

        return  $fieldset;
    }
}
