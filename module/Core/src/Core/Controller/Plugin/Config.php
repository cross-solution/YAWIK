<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Mapper\Query\Query;

class Config extends AbstractPlugin
{
    protected $config;
    protected $map = array();
    protected $applicationMap = array();
    protected $configAccess;

    public function __invoke($key = null, $all = False)
    {
        if (!isset($this->configAccess)) {
            $controller = $this->getController();
            $this->configAccess  = $controller->getServiceLocator()->get('configaccess');
        }
        
        $erg = array();
        if (isset($this->configAccess)) {
            $this->configAccess->setController($this->getController());
            $erg = $all?$this->configAccess->getByKey($key):$this->configAccess->get($key);
        }
        
        return $erg;
        
        //return $all?$this->getByKey($key):$this->get($key);
    }
    
    public function get($key = null)
    {
        if (isset($this->map[$key])) {
            $module          = $this->getNamespace();
            return isset($this->map[$key][$module]) ? $this->map[$key][$module] : array();
        }
        $config = $this->getConfig();
        if ($key) {
            return isset($config[$key]) ? $config[$key] : null;
        }
        return $config;
    }
    
    /**
     * fetch the settings for a certain key of all Modules 
     * @param string $key
     * @return array
     */
    public function getByKey($key = null)
    {
        if (!array_key_exists($key, $this->applicationMap)) {
            $this->applicationMap[$key] = array();
            $controller      = $this->getController();
            $config          = $controller->getServiceLocator()->get('Config');
            $appConfig       = $controller->getServiceLocator()->get('applicationconfig');
            foreach ($appConfig['modules'] as $module) {
                if (array_key_exists($module, $config)) {
                    if (array_key_exists($key, $config[$module])) {
                        $this->applicationMap[$key][$module] = $config[$module][$key];
                    }
                }
            }
        }
        return $this->applicationMap[$key];
    }
    
    protected function getConfig()
    {
        if (!$this->config) {
            $controller      = $this->getController();
            $module          = $this->getNamespace();
            $config          = $controller->getServiceLocator()->get('Config');
            $this->config    = isset($config[$module]) ? $config[$module] : array();
        }
        return $this->config;
    }
    
    protected function getMapConfig($key)
    {
        if (!isset($this->map[$key])) {
            $controller      = $this->getController();
            $config          = $controller->getServiceLocator()->get('Config');
            $this->map[$key] = isset($config[$key]) ? $config[$key] : array();
        }
        if (!empty($this->map[$key])) {
            $module          = $this->getNamespace();
            return isset($this->map[$key][$module])?$this->map[$key][$module]:array();
        }
        return array();
    }
    
    protected function getNamespace() {
            $controller      = $this->getController();
            $controllerClass = get_class($controller);
            $namespace       = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            return $namespace;
    }
    
}
