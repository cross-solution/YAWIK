<?php

namespace Core\Repository;

use Zend\ServiceManager\AbstractPluginManager;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Core\Repository\Mapper\MapperAwareInterface;
use Zend\ServiceManager\ConfigInterface;
use Core\Repository\EntityBuilder\EntityBuilderAwareInitializer;
use Core\Repository\Mapper\MapperAwareInitializer;


class RepositoryManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        
        parent::__construct($configuration);
        $this->addInitializer(new RepositoryAwareInitializer())
             ->addInitializer(new EntityBuilderAwareInitializer())
             ->addInitializer(new MapperAwareInitializer());
        
    }
    
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf RepositoryInterface) {
            die (__METHOD__. ': Plugin must implement RepositoryInterface');
        }
        
        
    }
}