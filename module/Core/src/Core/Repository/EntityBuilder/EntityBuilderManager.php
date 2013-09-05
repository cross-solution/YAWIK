<?php

namespace Core\Repository\EntityBuilder;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Core\Repository\Mapper\MapperAwareInitializer;
use Core\Repository\RepositoryAwareInitializer;


class EntityBuilderManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addInitializer(new EntityBuilderAwareInitializer())
             ->addInitializer(new MapperAwareInitializer())
             ->addInitializer(new RepositoryAwareInitializer());
    }
    
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf EntityBuilderInterface) {
            die (__METHOD__. ': Plugin must implement EntityBuilderInterface');
        }
        
        
    }
}