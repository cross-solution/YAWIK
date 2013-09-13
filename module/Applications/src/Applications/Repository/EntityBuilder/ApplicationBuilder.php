<?php

namespace Applications\Repository\EntityBuilder;

use Core\Repository\EntityBuilder\AggregateBuilder;
use Core\Repository\RepositoryAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\RelationEntity;
use Core\Entity\RelationCollection;

class ApplicationBuilder extends AggregateBuilder implements RepositoryAwareInterface
{
    protected $repositories;
    
    public function setRepositoryManager(ServiceLocatorInterface $repositoryManager)
    {
        $this->repositories = $repositoryManager;
        return $this;
    }
    
    public function getRepositoryManager()
    {
        return $this->repositories;
    }
    
    public function build($data = array())
    {
        $entity = parent::build($data);
        $job = new RelationEntity(
            array($this->repositories->get('job'), 'find'),
            array($entity->jobId)
        );
        $entity->injectJob($job);
        return $entity;
    }
}