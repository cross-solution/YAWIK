<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Factory\Form;

use Core\Entity\Hydrator\EntityHydrator;
use Organizations\Entity\EmployeePermissions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;
use Organizations\Entity\EmployeePermissionsInterface as Perms;
use Organizations\Form\EmployeeFieldset;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test, extract hydrating strategies
 * @since 0.18
 */
class EmployeeFieldsetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $serviceLocator \Zend\ServiceManager\AbstractPluginManager */
        $services = $serviceLocator->getServiceLocator();
        $fieldset = new EmployeeFieldset();
        $hydrator = new EntityHydrator();
        $repositories = $services->get('repositories');
        $users        = $repositories->get('Auth/User'); /* @var $users \Auth\Repository\User */

         /* todo: WRITE own Hydrator strategy class */
        $strategy = new ClosureStrategy(
            function($object) use ($users)
            {
                if (is_string($object)) {
                    return $users->find($object);
                }
                return $object;
            },
            function ($data) use ($users)
            {
                if (is_string($data)) {
                    $data = $users->find($data);
                }
                return $data;
            }
        );

        /* todo: write own strategy class */
        $permStrategy = new ClosureStrategy(
            // extract
            function($object) {
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
            function($data) {
                $permissions = array_reduce($data, function($c, $i) { return $c | $i; }, 0);
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