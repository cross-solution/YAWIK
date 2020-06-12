<?php
/**
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Applications\Factory\Auth\Dependency;

use Applications\Auth\Dependency\ListListener;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ListListenerFactory implements FactoryInterface
{
    /**
     * Create a ListListener listener
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ListListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ListListener($container->get('repositories')->get('Applications'));
    }
    /**
     * @see \Laminas\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ListListener::class);
    }
}
