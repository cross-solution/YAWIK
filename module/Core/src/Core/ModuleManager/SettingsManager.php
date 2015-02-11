<?php

namespace Core\ModuleManager;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;


class SettingsManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
    }
    
    public function validatePlugin($plugin)
    {
        //if (!$plugin instanceOf RepositoryInterface) {
        //    die (__METHOD__. ': Plugin must implement RepositoryInterface');
        //}
        
        return true;
    }
    
    public function setService($name, $service, $shared = true) {
        parent::setService($name, $service, $shared);
    }
}