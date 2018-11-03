<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Core\Form\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Form\Container as FormContainer;

/**
 * This is common form initializer
 *
 * @author fedys
 * @since 0.26
 */
class Initializer implements InitializerInterface
{
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof FormContainer) {
            $instance->setFormElementManager($container->get('FormElementManager'));
        }
    }
    
    
    /**
     * @see \Zend\ServiceManager\InitializerInterface::initialize()
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof \Core\Form\Container) {
            $instance->setFormElementManager($serviceLocator);
        }
    }
}
