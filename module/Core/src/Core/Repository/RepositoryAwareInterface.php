<?php

namespace Core\Repository;

use Core\Entity\EntityInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

interface RepositoryAwareInterface 
{
    
    public function setRepositoryManager(ServiceLocatorInterface $repositoryManager);
    public function getRepositoryManager();
    
}