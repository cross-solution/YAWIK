<?php

namespace Applications\Repository;

use Core\Model\ModelInterface;
use Core\Model\RelationCollection;


class Application 
{
    const LOAD_EAGER = true;
    const LOAD_LAZY  = false;
    
    protected $applicationMapper;
    protected $educationMapper;
    
    protected $applicationBuilder;
    protected $educationBuilder;
    
    
    
    protected $applicationModelPrototype;
    protected $educationModelPrototype;
    
    protected $applicationModelHydrator;
    protected $educationModelHydrator;
    
    public function __construct($applicationMapper, $educationMapper)
    {
        $this->applicationMapper = $applicationMapper;
        $this->educationMapper = $educationMapper;
        
    }
    
    public function getApplicationBuilder()
    {
        if (!$this->applicationBuilder) {
            $builder = new EntityBuilder\ApplicationBuilder($this);
            $this->setApplicationBuilder($builder);
        }
        return $this->applicationBuilder;
    }
    
    public function setApplicationBuilder($modelBuilder)
    {
        $this->applicationBuilder = $modelBuilder;
        return $this;
    }
    
    public function getEducationBuilder()
    {
        if (!$this->educationBuilder) {
            $builder = new EntityBuilder\EducationBuilder();
            $this->setEducationBuilder($builder);
        }
        return $this->educationBuilder;
    }
    
    public function setEducationBuilder($modelBuilder)
    {
        $this->educationBuilder = $modelBuilder;
        return $this;
    }
    
    
	public function find ($id, $loadDependencies = self::LOAD_LAZY)
    {
        $applicationData = $loadDependencies
              ? $this->applicationMapper->find($id)
              : $this->applicationMapper->find($id, 
                      array('educations'),
                      /*exclude*/ true
              );
        
        
        $model = $this->getApplicationBuilder()->buildModel($applicationData);
        return $model;
    }
    
    public function fetchAll($loadDependencies = self::LOAD_LAZY)
    {
        $cursor = $this->applicationMapper->fetchAll(array(), array(
            'educations'
        ), true);
        
        $collection = $this->getApplicationBuilder()->buildCollection(iterator_to_array($cursor));
        return $collection;
    }
    
    public function getPaginatorAdapter(array $propertyFilter, $sort)
    {
        $query = array();
        foreach ($propertyFilter as $property => $value) {
            if (in_array($property, array('jobId'))) {
                $query[$property] = new \MongoRegex('/^' . $value . '/');
            }
        }
        $cursor = $this->applicationMapper->fetchAll($query, array('educations'), true);
        $cursor->sort($sort);
        return new ApplicationPaginatorAdapter($cursor, $this->getApplicationBuilder());
    }
    
    public function fetchEducations($applicationModelOrId)
    {
        $id = $applicationModelOrId instanceOf \Applications\Model\Application
            ? $applicationModelOrId->getId()
            : $applicationModelOrId;
        
        $educationsData = $this->educationMapper->fetchByApplicationId($id);
        
        
        $collection = $this->getEducationBuilder()->buildCollection((array) $educationsData);
        return $collection;
        
    } 
    
    public function save($application)
    {
        $data = $this->getApplicationBuilder()->unbuild($application);
        
        $id = $this->applicationMapper->save($data);
        $application->setId($id);
    }
    
     
}