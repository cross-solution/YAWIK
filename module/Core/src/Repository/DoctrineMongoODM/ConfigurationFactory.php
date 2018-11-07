<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ConfigurationFactory.php */
namespace Core\Repository\DoctrineMongoODM;

use DoctrineMongoODMModule\Service\ConfigurationFactory as DMOMConfigurationFactory;

class ConfigurationFactory extends DMOMConfigurationFactory
{
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        /** @var $options \DoctrineMongoODMModule\Options\Configuration */
        $options = $this->getOptions($serviceLocator, 'configuration');
    
        $config = new ServiceLocatorAwareConfiguration;
    
        
        $config->setServiceLocator($serviceLocator);
        
        // logger
        if ($options->getLogger()) {
            $logger = $serviceLocator->get($options->getLogger());
            $config->setLoggerCallable(array($logger, 'log'));
        }
    
        // proxies
        $config->setAutoGenerateProxyClasses($options->getGenerateProxies());
        $config->setProxyDir($options->getProxyDir());
        $config->setProxyNamespace($options->getProxyNamespace());
    
        // hydrators
        $config->setAutoGenerateHydratorClasses($options->getGenerateHydrators());
        $config->setHydratorDir($options->getHydratorDir());
        $config->setHydratorNamespace($options->getHydratorNamespace());
    
        // default db
        $config->setDefaultDB($options->getDefaultDb());
    
        // caching
        $config->setMetadataCacheImpl($serviceLocator->get($options->getMetadataCache()));
    
        // retries
        $config->setRetryConnect($options->getRetryConnect());
        $config->setRetryQuery($options->getRetryQuery());
    
        // Register filters
        foreach ($options->getFilters() as $alias => $class) {
            $config->addFilter($alias, $class);
        }
    
        // the driver
        $config->setMetadataDriverImpl($serviceLocator->get($options->getDriver()));
    
        // metadataFactory, if set
        if ($factoryName = $options->getClassMetadataFactoryName()) {
            $config->setClassMetadataFactoryName($factoryName);
        }
    
        return $config;
    }
}
