<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Organizations\Factory\ImageFileCache;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Organizations\ImageFileCache\ApplicationListener;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ApplicationListenerFactory implements FactoryInterface
{

    /**
     * Create a ApplicationListener
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ApplicationListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = $container->get('Organizations\ImageFileCache\Manager');
        $repository = $container->get('repositories')->get('Organizations/OrganizationImage');

        return new ApplicationListener($manager, $repository);
    }
}
