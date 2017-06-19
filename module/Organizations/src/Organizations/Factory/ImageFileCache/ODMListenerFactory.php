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
use Organizations\ImageFileCache\ODMListener;

/**
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ODMListenerFactory implements FactoryInterface
{
    /**
     * Create a ODMListener
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ODMListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ODMListener($container->get('Organizations\ImageFileCache\Manager'));
    }
}
