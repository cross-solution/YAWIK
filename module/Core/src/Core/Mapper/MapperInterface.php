<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core mappers */
namespace Core\Mapper;

use Core\Model\ModelInterface;
use Core\Mapper\Criteria\CriteriaInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Mapper interface
 */
interface MapperInterface
{

    /**
     * Sets the model prototype.
     *  
     * @param ModelInterface $model
     */
    public function setModelPrototype(ModelInterface $model);
    
    /**
     * Sets the model hydrator.
     * 
     * @param HydratorInterface $hydrator
     */
    public function setModelHydrator(HydratorInterface $hydrator);
    
    /**
     * Gets the model hydrator.
     *
     * @return HydratorInterface
     */
    public function getModelHydrator();
        
    /**
     * Finds the model by its id.
     * 
     * @param mixed $id
     * @return \Core\Model\ModelInterface
     */
    public function find($id);
    
    /**
     * Fetches all models.
     * 
     * @return \Core\Model\CollectionInterface
     */
    public function fetchAll($criteria=null);
    
    /**
     * Creates a new model from the prototype.
     * 
     * Populates the model with the properties passed as
     * <code>$data</code>.
     * 
     * @param array $data
     * @return \Core\Model\ModelInterface
     */
    public function create(array $data=array());
    
    /**
     * Saves a model.
     * 
     * @param ModelInterface $model
     */
    public function save(ModelInterface $model);
    
}