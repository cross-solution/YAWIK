<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Core\Form\Service;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * This is common form initializer
 *
 * @author fedys
 * @since 0.26
 */
class Initializer implements InitializerInterface
{

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
