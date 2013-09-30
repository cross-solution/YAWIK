<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Mapper\Query\Query;

class Config extends AbstractPlugin
{
    protected $config;
    protected $map = array();

    public function __invoke($key = null)
    {
        return $this->get($key);
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
