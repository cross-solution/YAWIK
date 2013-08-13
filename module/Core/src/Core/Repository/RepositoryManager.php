<?php

namespace Core\Repository;

use Zend\ServiceManager\AbstractPluginManager;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;
use Core\Repository\Mapper\MapperAwareInterface;


class RepositoryManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $self = $this;
        $this->addInitializer(function ($instance) use ($self) {
            if ($instance instanceof EntityBuilderAwareInterface) {
                $instance->setEntityBuilderManager($self->getServiceLocator()->get('builders'));
            }
        });
        
        $this->addInitializer(function ($instance) use ($self) {
            if ($instance instanceOf MapperAwareInterface) {
                $instance->setMapperManager($self->getServiceLocator()->get('mappers'));
            }
        });
    }
    
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf RepositoryInterface) {
            die (__METHOD__. ': Plugin must implement RepositoryInterface');
        }
        
        
    }
}