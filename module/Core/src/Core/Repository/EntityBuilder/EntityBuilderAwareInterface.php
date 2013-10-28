<?php

namespace Core\Repository\EntityBuilder;


use Zend\ServiceManager\ServiceLocatorInterface;
interface EntityBuilderAwareInterface
{
    public function setEntityBuilderManager(ServiceLocatorInterface $entityBuilderManager);
    public function getEntityBuilderManager();
}