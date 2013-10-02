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
        if (isset($data['attachments'])) {
            $attachmentsIds = $data['attachments'];
            unset($data['attachments']);
        } else {
            $attachmentsIds = false;
        }
        $entity = parent::build($data);
        if (!$entity->job) {
            $job = new RelationEntity(
                array($this->repositories->get('job'), 'find'),
                array($entity->jobId)
            );
            $entity->injectJob($job);
        }
        if (false === $attachmentsIds) {
            $entity->setAttachments($this->getCollection());
        } else {
            $entity->setAttachments(new RelationCollection(
                array($this->mappers->get('Applications/Files'), 'fetchByIds'),
                array($attachmentsIds)
            ));
        }
        return $entity;
    }
}