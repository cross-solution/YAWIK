<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Auth\Dependency;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Exception\RuntimeException;
use IteratorAggregate;
use Countable;
use ArrayIterator;

class ModuleManager extends AbstractPluginManager implements IteratorAggregate, Countable
{
    
    /**
     * @var array
     */
    protected $modules;
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Auth\Dependency\ModuleManager
     */
    public static function factory(ServiceLocatorInterface $serviceLocator)
    {
        return new static($serviceLocator, $serviceLocator->get('Config')['auth_dependency_module_manager']);
    }
    
    /**
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getModules());
    }
    
    /**
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->getModules());
    }
    
    /**
	 * @see \Zend\ServiceManager\AbstractPluginManager::validatePlugin()
	 */
	public function validatePlugin($plugin)
	{
		if (!$plugin instanceof ModuleInterface)
		{
		    throw new RuntimeException(sprintf('Plugin must be instance of %s', ModuleInterface::class));
		}
	}
	
	/**
	 * @return ModuleInterface[]
	 */
	protected function getModules()
	{
	    if (!isset($this->modules))
	    {
    	    $this->modules = [];
    	    $availablesServices = [];
    	    $registeredServices = [
    	        'invokableClasses' => array_keys($this->invokableClasses),
    	        'factories' => array_keys($this->factories)
    	    ];
    	
    	    foreach ($registeredServices as $services)
    	    {
    	        $availablesServices = array_merge($availablesServices, $services);
    	    }
    	
    	    foreach (array_unique($availablesServices) as $service)
    	    {
    	        $this->modules[$service] = $this->get($service);
    	    }
	    }
	
	    return $this->modules;
	}
}
