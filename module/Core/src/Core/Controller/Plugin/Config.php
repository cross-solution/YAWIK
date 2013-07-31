<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Mapper\Query\Query;

class Config extends AbstractPlugin
{
    
    protected $config;

    public function __invoke($key = null)
    {
        return $this->get($key);
    }
    
    public function get($key = null)
    {
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
            $controllerClass = get_class($controller);
            $config          = $controller->getServiceLocator()->get('Config');
            $module          = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $this->config    = isset($config[$module]) ? $config[$module] : array();
        }
        return $this->config;
    }
    
}
