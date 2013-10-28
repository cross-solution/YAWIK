<?php

namespace Applications\Repository\EntityBuilder;

use Core\Repository\EntityBuilder\AggregateBuilder;
use Core\Repository\RepositoryAwareInterface;
use Core\Repository\Mapper\MapperAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\RelationEntity;
use Core\Entity\RelationCollection;

class ApplicationBuilder extends AggregateBuilder implements RepositoryAwareInterface, MapperAwareInterface
{
    protected $repositories;
    protected $mappers;
    
    public function setRepositoryManager(ServiceLocatorInterface $repositoryManager)
    {
        $this->repositories = $repositoryManager;
        return $this;
    }
    
    public function getRepositoryManager()
    {
        return $this->repositories;
    }
    
    public function setMapperManager(ServiceLocatorInterface $mapperManager)
    {
        $this->mappers = $mapperManager;
        return $this;
    }
    
    public function getMapperManager()
    {
        return $this->mappers;
    }
    
    public function build($data = array())
    {
        
        $entity = parent::build($data);
        
        if (!$entity->job) {
            $job = new RelationEntity(
                array($this->repositories->get('job'), 'find'),
                array($entity->jobId)
            );
            $entity->injectJob($job);
        }
        
        $attachments = isset($data['refs']['applications-files'])
            ? new RelationCollection(
                array($this->mappers->get('Applications/Files'), 'fetchByIds'),
                array($data['refs']['applications-files'])
              )
            : $this->getCollection();
        $entity->injectAttachments($attachments);
        
        return $entity;
    }
    
}