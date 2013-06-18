<?php

namespace Applications\Repository\MongoDb;

use Core\Model\ModelInterface;
use Core\Model\RelationCollection;


abstract class AbstractApplicationRepository
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
            $builder = new ModelBuilder\ApplicationBuilder($this);
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
            $builder = new ModelBuilder\EducationBuilder();
            $this->setEducationBuilder($builder);
        }
        return $this->educationBuilder;
    }
    
    public function setEducationBuilder($modelBuilder)
    {
        $this->educationBuilder = $modelBuilder;
        return $this;
    }
    
    public function fetchAllEducations($applicationModelOrId)
    {
        $id = $applicationModelOrId instanceOf \Applications\Model\Application
            ? $applicationModelOrId->getId()
            : $applicationModelOrId;
        
        $educationsData = $this->educationMapper->fetchByApplicationId($id);
        
        
        $collection = $this->getEducationBuilder()->buildCollection((array) $educationsData);
        return $collection;
        
    } 
    
   
     
}