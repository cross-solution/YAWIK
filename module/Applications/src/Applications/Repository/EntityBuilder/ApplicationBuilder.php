<?php

namespace Applications\Repository\EntityBuilder;

use Core\Repository\Hydrator;

class ApplicationBuilder extends AbstractEntityBuilder
{
    protected $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
        parent::__construct();
        
    }
    
    protected function constructEntityPrototype()
    {
        return new \Applications\Entity\Application();
    }
    
    protected function constructHydrator()
    {
        $hydrator = parent::constructHydrator();
        $hydrator->addStrategy('dateCreated', new Hydrator\DatetimeStrategy())
                 ->addStrategy('dateModified', new Hydrator\DatetimeStrategy(/*extractResetDate*/ true))
                 ->addStrategy('educations', new Hydrator\SubDocumentsStrategy($this->repository->getEducationBuilder()));
        
        return $hydrator;
    }
    
    public function build($data = null)
    {
        $entity = parent::build($data);
        if (!isset($data['educations'])) {
            $entity->setEducations(new \Core\Entity\RelationCollection(
                array($this->repository, 'fetchEducations'),
                array($entity->id)
            ));
        }
        return $entity;
    }
}