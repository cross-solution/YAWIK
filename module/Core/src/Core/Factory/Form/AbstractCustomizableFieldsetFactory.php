<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory\Form;

use Core\Form\CustomizableFieldsetInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
abstract class AbstractCustomizableFieldsetFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    const OPTIONS_NAME = '';
    const CLASS_NAME   = '';

    protected $options;

    /**
     * Set creation options
     *
     * @param  array $options
     *
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    protected function resetCreationOptions()
    {
        $this->options = null;
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var CustomizableFieldsetInterface $instance */
        $instance = $this->createFormInstance($container, $requestedName, $options);

        if (!$instance instanceOf CustomizableFieldsetInterface) {
            throw new \RuntimeException('Form or Fieldset instance must implement ' . CustomizableFieldsetInterface::class);
        }

        $customizeOptions = $this->getCustomizationOptions($container, $requestedName, $options);
        $instance->setCustomizationOptions($customizeOptions);

        return $instance;
    }

    protected function getCustomizationOptions(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!static::OPTIONS_NAME) {
            throw new \RuntimeException('The class constants "OPTIONS_NAME" must be non empty.');
        }

        return $container->get(static::OPTIONS_NAME);
    }

    protected function createFormInstance(ContainerInterface $container, $name, array $options = null)
    {
        if (!static::CLASS_NAME || !class_exists(static::CLASS_NAME)) {
            throw new \RuntimeException('The class constants "CLASS_NAME" must be non empty and name an existent class.');
        }

        $class = static::CLASS_NAME;

        return new $class(null, $options);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface|\Zend\ServiceManager\AbstractPluginManager $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $instance = $this($serviceLocator->getServiceLocator(), '', $this->options);
        $this->resetCreationOptions();

        return $instance;
    }
}