<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Core\Factory\Controller;

use Core\EventManager\EventManager;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\I18n\Translator;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Validator\ValidatorPluginManager;

/**
 * Install module main controller.
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @since  0.29
 */
class LazyControllerFactory implements AbstractFactoryInterface
{
    protected $aliases = [
        FormElementManager::class => 'FormElementManager',
        ValidatorPluginManager::class => 'ValidatorManager',
        Translator::class => 'translator',
        ModuleManagerInterface::class => 'ModuleManager',
        EventManager::class => 'Core/EventManager',
        RepositoryService::class => 'repositories',
    ];
    
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return strstr($requestedName, '\Controller') !== false;
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $className = $this->getClassName($requestedName);
        $class = new \ReflectionClass($className);

        if ($constructor = $class->getConstructor()) {
            if ($params = $constructor->getParameters()) {
                $constructorArgs = [];
                foreach ($params as $p) {
                    $serviceName = '';
                    if ($p->getClass()) {
                        $serviceName = $p->getClass()->getName();
                        if (array_key_exists($serviceName, $this->aliases)) {
                            $serviceName = $this->aliases[$serviceName];
                        }
                    } else {
                        if ($p->getName() == 'config') {
                            $serviceName = 'config';
                        }
                    }

                    if (!$container->has($serviceName)) {
                        throw new ServiceNotCreatedException(sprintf(
                            'Can\'t create constructor argument "%s" for service "%s"',
                            $p->getName(),
                            $requestedName
                        ));
                    }
                    try {
                        $constructorArgs[] = $container->get($serviceName);
                    } catch (\Exception $x) {
                        echo __CLASS__ . " couldn't create an instance of {$p->getName()} to satisfy the constructor for $requestedName.";
                        exit;
                    }
                }
                return $class->newInstanceArgs($constructorArgs);
            }
        }
        
        return new $className;
    }

    /**
     * Generate class name
     *
     * @param string $requestedName
     * @return string
     */
    private function getClassName($requestedName)
    {
        $exp = explode('/', $requestedName);

        $className = array_shift($exp).'\\Controller\\'.implode('\\', $exp).'Controller';
        if (!class_exists($className)) {
            throw new ServiceNotCreatedException(
                sprintf(
                    'Can\'t find correct controller class for "%s"',
                    $requestedName
                )
            );
        }

        return $className;
    }
}
