<?php

namespace Jobs\Repository\EntityBuilder;

use Core\Repository\EntityBuilder\EntityBuilder;
use Core\Repository\RepositoryAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\RelationEntity;
use Core\Entity\RelationCollection;

class JobBuilder extends EntityBuilder implements RepositoryAwareInterface
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
        $applications = new RelationCollection(
            array($this->repositories->get('application'), 'fetchByJobId'),
            array($entity->id)
        );
        $entity->injectApplications($applications);
        if ($entity->userId) {
            $user = new RelationEntity(
                array($this->repositories->get('user'), 'find'),
                array($entity->userId)
            );
            $entity->injectUser($user);
        }
        return $entity;
    }
}