<?php

namespace Core\Repository\Mapper;

use Zend\ServiceManager\AbstractPluginManager;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Core\Repository\EntityBuilder\EntityBuilderAwareInitializer;
use Core\Repository\RepositoryAwareInitializer;
use Zend\ServiceManager\ConfigInterface;


class MapperManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addInitializer(new MapperAwareInitializer())
             ->addInitializer(new EntityBuilderAwareInitializer())
             ->addInitializer(new RepositoryAwareInitializer());
        
    }
    
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf MapperInterface) {
            die (__METHOD__. ': Plugin must implement MapperInterface');
        }
        
        
    }
}