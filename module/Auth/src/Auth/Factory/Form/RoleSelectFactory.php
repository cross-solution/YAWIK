<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

namespace Auth\Factory\Form;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Form\Element\Select;
use Auth\Entity\User;

/**
 * Class RoleSelectFactory
 *
 * Creates the select box of roles used temporary on the users profiles page. Box is removed from the
 * Users profiles Page now. But maybe we can reuse the code for the admin user
 *
 * @package Auth\Factory\Form
 */
class RoleSelectFactory implements FactoryInterface
{
    /**
     * Create a Select Element
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return Select
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config     = $container->get('Config');
        $translator = $container->get('translator');

        $publicRoles = isset($config['acl']['public_roles'])
                       && is_array($config['acl']['public_roles'])
                       && !empty($config['acl']['public_roles'])
            ? $config['acl']['public_roles']
            : (in_array(User::ROLE_USER, $config['acl']['roles'])
               || array_key_exists('user', $config['acl']['roles'])
                ? array(User::ROLE_USER)
                : array('none')
            );

        $valueOptions = array();
        foreach ($publicRoles as $role) {
            $valueOptions[$role] = $translator->translate($role);
        }

        $select = new Select('role');
        $select->setValueOptions($valueOptions);

        return $select;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), Select::class);
    }
}
