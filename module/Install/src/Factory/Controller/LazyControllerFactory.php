<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

namespace Install\Factory\Controller;

use Core\Service\ClearCacheService;
use Interop\Container\ContainerInterface;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\Mvc\I18n\Translator;
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
        ClearCacheService::class => ClearCacheService::class,
    ];
    
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        list($module, ) = explode('\\', __NAMESPACE__, 2);
        return strstr($requestedName, $module . '\Controller') !== false;
    }
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $class = new \ReflectionClass($requestedName);
        if ($constructor = $class->getConstructor()) {
            if ($params = $constructor->getParameters()) {
                $parameter_instances = [];
                foreach ($params as $p) {
                    if ($p->getClass()) {
                        $cn = $p->getClass()->getName();
                        if (array_key_exists($cn, $this->aliases)) {
                            $cn = $this->aliases[$cn];
                        }
                        
                        try {
                            $parameter_instances[] = $container->get($cn);
                        } catch (\Exception $x) {
                            echo __CLASS__
                                 . " couldn't create an instance of $cn to satisfy the constructor for $requestedName.";
                            exit;
                        }
                    } else {
                        if ($p->isArray() && $p->getName() == 'config') {
                            $parameter_instances[] = $container->get('config');
                        }
                    }
                }
                return $class->newInstanceArgs($parameter_instances);
            }
        }
        
        return new $requestedName;
    }
}
