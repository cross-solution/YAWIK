<?php

namespace Core\Repository\Mapper;

use Zend\ServiceManager\AbstractPluginManager;
use Core\Repository\EntityBuilder\EntityBuilderAwareInterface;


class MapperManager extends AbstractPluginManager
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
    }
    
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceOf MapperInterface) {
            die (__METHOD__. ': Plugin must implement MapperInterface');
        }
        
        
    }
}