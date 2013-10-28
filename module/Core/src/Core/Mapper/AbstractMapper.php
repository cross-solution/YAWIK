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
use Core\Model\CollectionInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;
use Core\Model\Hydrator\ModelHydrator;

/**
 * Partial implementation of \Core\Mapper\MapperInterface.
 * 
 * Implements the methods
 *
 * - setModelPrototype()
 *   Stores the model prototype as protected class member.
 *   
 * - create()
 * 
 */
abstract class AbstractMapper implements MapperInterface
{

    /**
     * The model prototype.
     * 
     * @var \Core\Model\ModelInterface
     */
    protected $_modelPrototype;
    
    protected $modelCollectionPrototype;
    
    /**
     * The model hydrator.
     * 
     * @var \Zend\Stdlib\Hydrator\HydratorInterface
     */
    protected $modelHydrator;
     
    protected $queryConverter;
    
    /**
     * {@inheritdoc}
     * 
     * @see \Core\Mapper\MapperInterface::setModelPrototype()
     * @return \Core\Mapper\AbstractMapper
     */
    public function setModelPrototype(ModelInterface $model)
    {
        $this->_modelPrototype = $model;
        return $this;
    }
    
    public function setModelCollectionProtoype(CollectionInterface $collection)
    {
        $this->modelCollectionPrototype = $collection;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @see \Core\Mapper\MapperInterface::setModelHydrator()
     * @return \Core\Mapper\AbstractMapper
     */
    public function setModelHydrator(HydratorInterface $hydrator)
    {
        $this->modelHydrator = $hydrator;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @see \Core\Mapper\MapperInterface::getModelHydrator()
     */
    public function getModelHydrator()
    {
        if (!$this->modelHydrator) {
            $hydrator = new ModelHydrator();
            $this->setModelHydrator($hydrator);
        }
        return $this->modelHydrator;
    }
    
    public function convertQuery(\Core\Mapper\Query\Query $query)
    {
        $converter = $query->getServiceManager()->get('query_converter');
        return $converter->convert($query, $this);
    }
    
	/**
     * {@inheritdoc}
     */
    public function create(array $data=array())
    {
        $model = clone $this->_modelPrototype;
        $hydrator = $this->getModelHydrator();
        $hydrator->hydrate($data, $model);
        //$model->setData($data);
        return $model;
    }
    
    public function createCollection(array $data=array())
    {
        if (!$this->modelCollectionPrototype) {
            $this->setModelCollectionProtoype(new \Core\Model\Collection());
        }
        $collection = clone $this->modelCollectionPrototype;
        foreach ($data as $modelData) {
            
            $model = $modelData instanceOf \Core\Model\ModelInterface ? $modelData : $this->create($modelData);
            $collection->addModel($model);
        }
        return $collection;
    }
}