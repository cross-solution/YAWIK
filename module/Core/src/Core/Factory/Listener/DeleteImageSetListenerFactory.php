<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory\Listener;

use Core\Listener\DeleteImageSetListener;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class DeleteImageSetListenerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repositories = $container->get('repositories');
        $config       = $container->get('Config');
        $config       = isset($config[DeleteImageSetListener::class])
                      ? $config[DeleteImageSetListener::class]
                      : [];

        $listener = new DeleteImageSetListener($repositories, $config);

        return $listener;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, DeleteImageSetListener::class);
    }
}