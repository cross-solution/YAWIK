<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Applications\Factory\Auth\Dependency;

use Applications\Auth\Dependency\ListListener;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ListListener::class);
    }
}
