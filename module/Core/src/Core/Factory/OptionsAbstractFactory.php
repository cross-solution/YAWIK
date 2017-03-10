<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\AbstractOptions;

/**
 * Creates options instances from configuration specifications.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.23
 */
class OptionsAbstractFactory implements AbstractFactoryInterface
{

    /**
     * Simple mode handles only skalar values, arrays and POPOs.
     */
    const MODE_SIMPLE = 'simple';

    /**
     * Nested mode is able to handle other options instances as an option value.
     */
    const MODE_NESTED = 'nested';

    /**
     * The configuration specification of all creatable options instances.
     *
     * The format is
     * <pre>
     * [
     *      'ServiceNameOfTheOptionsInstance' => [
     *          'class' => 'FQCN of the options class.',
     *          'mode' => SIMPLE or NESTED (optional, default SIMPLE)
     *          'options' => [
     *              'simpleSkalar' => 'value',
     *              'simpleArray' => [ 'someArray' ],
     *              'nestedOptions' => [
     *                  '__class__' => 'FQCN of the nested options class.',
     *                  'someValue' => 'skalar'
     *              ],
     *          ],
     *      ],
     * ];
     *
     * @var array
     */
    protected $optionsConfig;

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName();
    }

    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     *
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName);
    }


    /**
     * Determines if we can create an options instance with name.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param string                  $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Load options config specifications the first time this method is called.
        if (null === $this->optionsConfig) {
            $mainConfig          = $serviceLocator->get('config');
            $this->optionsConfig = isset($mainConfig['options']) ? $mainConfig['options'] : [];
        }
        return false !== $this->getOptionsConfig($requestedName, $name);
    }

    /**
     * Creates an options instance  with name.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param string                  $requestedName
     *
     * @return AbstractOptions
     * @throws \InvalidArgumentException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getOptionsConfig($requestedName, $name);

        if (!isset($config['class']) && !class_exists($requestedName)) {
            throw new \InvalidArgumentException(sprintf(
                                                    'Missing index "class" from the config array for options "%s"',
                                                    $requestedName
                                                ));
        }

        $className = isset($config['class']) ? $config['class'] : $requestedName;
        $mode      = isset($config['mode']) ? $config['mode'] : self::MODE_SIMPLE;
        $options   = isset($config['options']) ? $config['options'] : [];

        if (self::MODE_SIMPLE == $mode) {
            return new $className($options);
        }

        if (self::MODE_NESTED == $mode) {
            return $this->createNestedOptions($className, $options);

        }

        throw new \InvalidArgumentException(sprintf('Unknown mode "%s".', $mode));
    }


    /**
     * Creates a nested options instance.
     *
     * @param string $className
     * @param array  $options
     *
     * @return AbstractOptions
     */
    protected function createNestedOptions($className, $options)
    {
        $class = new $className();

        foreach ($options as $key => $spec) {
            if (is_array($spec) && array_key_exists('__class__', $spec)) {
                $nestedClassName = $spec['__class__'];
                unset($spec['__class__']);
                $spec = $this->createNestedOptions($nestedClassName, $spec);
            }

            $class->{$key} = $spec;
        }

        return $class;
    }

    /**
     * Gets the configuration for a specific options instance.
     *
     * Returns FALSE if configuration cannot be found.
     *
     * @param string $fullName
     * @param string $normalizedName
     *
     * @return array|bool
     */
    protected function getOptionsConfig($fullName, $normalizedName)
    {
        if (array_key_exists($fullName, $this->optionsConfig)) {
            return $this->optionsConfig[$fullName];
        }

        if (array_key_exists($normalizedName, $this->optionsConfig)) {
            return $this->optionsConfig[$normalizedName];
        }

        return false;
    }
}