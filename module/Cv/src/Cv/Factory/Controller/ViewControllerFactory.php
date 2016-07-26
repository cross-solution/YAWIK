<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Cv\Factory\Controller;

use Cv\Controller\ViewController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class ViewControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get('repositories')->get('Cv/Cv');

        return new ViewController($repository);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string|null             $name
     * @param string|null             $requestedName
     *
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $container = $serviceLocator->getServiceLocator();

        return $this($container, $requestedName ?: ViewController::class);
    }


}