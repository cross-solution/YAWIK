<?php

namespace Applications\Repository\MongoDb\ModelBuilder;

use Core\Repository\MongoDb\Hydrator;

class ApplicationBuilder extends AbstractModelBuilder
{
    protected $repository;
    
    public function __construct($repository)
    {
        $this->repository = $repository;
        parent::__construct();
        
    }
    
    protected function constructModelPrototype()
    {
        return new \Applications\Model\Application();
    }
    
    protected function constructHydrator()
    {
        $hydrator = parent::constructHydrator();
        $hydrator->addStrategy('dateCreated', new Hydrator\DatetimeStrategy())
                 ->addStrategy('dateModified', new Hydrator\DatetimeStrategy(/*extractResetDate*/ true))
                 ->addStrategy('educations', new Hydrator\SubDocumentsStrategy($this->repository->getEducationBuilder()));
        
        return $hydrator;
    }
    
    public function buildModel(array $data = array())
    {
        $model = parent::buildModel($data);
        if (!isset($data['educations'])) {
            $model->setEducations(new \Core\Model\RelationCollection(
                array($this->repository, 'fetchEducations'),
                array($model->id)
            ));
        }
        return $model;
    }
}