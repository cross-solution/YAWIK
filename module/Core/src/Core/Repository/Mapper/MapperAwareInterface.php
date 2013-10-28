<?php

namespace Core\Repository\Mapper;

use Zend\ServiceManager\ServiceLocatorInterface;
interface MapperAwareInterface
{
    public function setMapperManager(ServiceLocatorInterface $mapperManager);
    public function getMapperManager();
    
}