<?php

namespace Core\Mapper\Query\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\Config;

abstract class AbstractManagerFactory implements FactoryInterface
{
    protected $managerClass;
    protected $configKey;
    
	/* (non-PHPdoc)
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService (\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $pm = $this->getManagerInstance();
        
        $appConfig = $serviceLocator->get('Config');
        $configKey = $this->getConfigKey();
        if (isset($appConfig[$configKey])) {
            $config = new Config($appConfig[$configKey]);
            $config->configureServiceManager($pm);
        }

        return $pm;
    }
	/**
     * @return the $managerClass
     */
    public function getManagerClass ()
    {
        return $this->managerClass;
    }

    /**
     * @param field_type $managerClass
     * @return $this
     */
    public function setManagerClass ($managerClass)
    {
        $this->managerClass = $managerClass;
        return $this;
    }
    
    public function getManagerInstance ()
    {
        $class = $this->getManagerClass();
        return new $class;
    }

	/**
     * @return the $configKey
     */
    public function getConfigKey ()
    {
        if (!$this->configKey) {
            $classParts = explode('\\', $this->getManagerClass());
            $className = array_pop($classParts);
            
            $this->configKey = 'query' . preg_replace_callback(
                '~([A-Z])~', 
                function ($letters) {
                    $letter = array_shift($letters);
                    return '_' . strtolower($letter); 
                },
                $className
            );
        }
        return $this->configKey;
    }

	/**
     * @param field_type $configKey
     */
    public function setConfigKey ($configKey)
    {
        $this->configKey = $configKey;
        return $this;
    }


    
    
}
