<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class OptionsAbstractFactory implements AbstractFactoryInterface
{

    const MODE_SIMPLE = 'simple';
    const MODE_NESTED = 'nested';

    protected $optionsConfig;

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Load options config specifications the first time this method is called.
        if (null === $this->optionsConfig) {
            $mainConfig = $serviceLocator->get('config');
            $this->optionsConfig = isset($mainConfig['options']) ? $mainConfig['options'] : [];
        }

        return false !== $this->getOptionsConfig($requestedName, $name);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getOptionsConfig($requestedName, $name);

        if (!isset($config['class'])) {
            throw new \InvalidArgumentException(sprintf(
                'Missing index "class" from the config array for options "%s"',
                $requestedName
            ));
        }

        $className = $config['class'];
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