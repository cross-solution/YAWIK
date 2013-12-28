<?php

namespace Core\src\Core\Service;

use Zend\Log\LoggerServiceFactory;
use Zend\Log\Logger;
use Zend\ServiceManager\ServiceLocatorInterface;

class Log extends LoggerServiceFactory {

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        // Configure the logger
        $config = $serviceLocator->get('Config');
        $logConfig = isset($config['log']) ? $config['log'] : array();
        $logger = new Logger($logConfig);
        return $logger;
        
    }
}

?>