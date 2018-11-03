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
//use Zend\ServiceManager\FactoryInterface;
//use Zend\ServiceManager\MutableCreationOptionsInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\PluginManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory boilerplate for customizable Fieldsets
 *
 * Ideally, you just need to extends this and set the values of the two constants.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
abstract class AbstractCustomizableFieldsetFactory implements FactoryInterface, PluginManagerInterface
{
    /**
     * Name of the options service
     * @var string
     */
    const OPTIONS_NAME = '';

    /**
     * The target fieldset class FQN
     *
     * @var string
     */
    const CLASS_NAME   = '';

    /**
     * Creation options.
     *
     * @var array
     */
    protected $options;

    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Reset the creation options.
     */
    protected function resetCreationOptions()
    {
        $this->options = null;
    }

    /**
     * Creates an instance.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return CustomizableFieldsetInterface
     * @throws \RuntimeException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var CustomizableFieldsetInterface $instance */
        $instance = $this->createFormInstance($container, $requestedName, $options);

        if (!$instance instanceof CustomizableFieldsetInterface) {
            throw new \RuntimeException('Form or Fieldset instance must implement ' . CustomizableFieldsetInterface::class);
        }

        $customizeOptions = $this->getCustomizationOptions($container, $requestedName, $options);
        $instance->setCustomizationOptions($customizeOptions);

        return $instance;
    }

    /**
     * Get the customization options.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return \Core\Options\FieldsetCustomizationOptions
     * @throws \RuntimeException
     */
    protected function getCustomizationOptions(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!static::OPTIONS_NAME) {
            throw new \RuntimeException('The class constants "OPTIONS_NAME" must be non empty.');
        }

        return $container->get(static::OPTIONS_NAME);
    }

    /**
     * Create the form/fieldset instance
     *
     * @param ContainerInterface $container
     * @param string             $name
     * @param array              $options
     *
     * @return \Zend\Form\Fieldset
     * @throws \RuntimeException
     */
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
     * @internal
     *      proxies to {@link __invoke()}
     *
     * @param ServiceLocatorInterface|\Zend\ServiceManager\AbstractPluginManager $serviceLocator
     *
     * @return CustomizableFieldsetInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $instance = $this($serviceLocator, '', $this->options);
        $this->resetCreationOptions();

        return $instance;
    }

    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function has($id)
    {
        // TODO: Implement has() method.
    }

    public function validate($instance)
    {
        // TODO: Implement validate() method.
    }

    public function build($name, array $options = null)
    {
        // TODO: Implement build() method.
    }
}
