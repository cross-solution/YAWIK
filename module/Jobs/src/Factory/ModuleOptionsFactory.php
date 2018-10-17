<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace Jobs\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Jobs\Options\ModuleOptions;

/**
 * Class ModuleOptionsFactory
 * @package Jobs\Factory
 */
class ModuleOptionsFactory implements FactoryInterface
{
    /**
     * Create a ModuleOptions options
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ModuleOptions
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        $jobs_options = isset($config['jobs_options']) ? $config['jobs_options'] : array();

        if (!array_key_exists('multipostingApprovalMail', $jobs_options) || '' ==  trim($jobs_options['multipostingApprovalMail'])) {
            $coreOptions = $container->get('Core/Options');
            $jobs_options['multipostingApprovalMail'] = $coreOptions->getSystemMessageEmail();
        }

        return new ModuleOptions($jobs_options);
    }
}
