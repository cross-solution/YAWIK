<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Config extends AbstractPlugin
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     *
     * Call it with
     * (null, null): return array: all config for current module (auto-detect form controller class)
     * (string, true) = (null, string): return array: all config for given module (1.param)
     * (array, true) = (null, array): return array all config for given modules (1.param) modules w/o config are excluded.
     *
     * (string): return value of config key of current module or null
     * (string, string): return value of config key of given module or null
     * (string, array): return all values of config key from given modules.
     *
     * (array): return all values form given config keys from current module or empty array.
     * (array, string): return all values from gioven config keys from gievn module or empty array.
     * (array, array): return all values from given keys from all given modules.
     *
     * @param string $key
     * @param string $module
     * @return \Core\Controller\Plugin\Config|Ambigous <multitype:, multitype:Ambigous <NULL, multitype:> >|Ambigous <NULL, multitype:>
     */
    public function __invoke($key = null, $module = null)
    {
        return $this->get($key, $module);
    }
    
    protected function loop($array, $static, $asModule = false)
    {
        $result = array();
        foreach ($array as $item) {
            $value = $asModule ? $this->get($static, $item) : $this->get($item, $static);
            if ((is_array($value) && !count($value)) || null === $value) {
                continue;
            }
            $result[$item] = $value;
        }
        return $result;
    }
    
    public function get($key = null, $module = null, $filterEmpty = true)
    {
        if (true === $module) {
            $module = $key;
            $key    = null;
        }
        
        if (is_array($module)) {
            return $this->loop($module, $key, true);
        }
        
        if (is_array($key)) {
            return $this->loop($key, $module);
        }

        if (null === $module) {
            $module = $this->getCurrentModuleName();
        }
        
        if (null === $key) {
            return isset($this->config[$module])
                  ? $this->config[$module]
                  : array();
        }
        
        return isset($this->config[$module][$key])
               ? $this->config[$module][$key]
               : null;
    }
    
    public function __get($name)
    {
        return $this->get($name);
    }
    
    protected function getCurrentModuleName()
    {
        $controller      = $this->getController();
        $controllerClass = get_class($controller);
        $moduleName      = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        
        return $moduleName;
    }
}
