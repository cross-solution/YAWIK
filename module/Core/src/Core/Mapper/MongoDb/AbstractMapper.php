<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core MongoDb Mappers */
namespace Core\Mapper\MongoDb;

use Core\Mapper\MongoDb\MapperInterface;
use Core\Mapper\MongoDb\Hydrator\DatetimeStrategy;
use Core\Mapper\AbstractMapper as CoreAbstractMapper;
use Core\Mapper\Criteria\CriteriaInterface;
use Core\Model\Collection as ModelCollection;
use Core\Model\ModelInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Concrete implementation of \Core\Mapper\MongoDb\MapperInterface
 * 
 */
abstract class AbstractMapper extends CoreAbstractMapper implements MapperInterface
{
    
    /**
     * Mongo collection
     * 
     * @var \MongoCollection
     */
    protected $_collection;
    
    /**
     * {@inheritdoc}
     * @return \Core\Mapper\MongoDb\AbstractMapper
     * @see \Core\Mapper\MongoDb\MapperInterface::setCollection()
     */
    public function setCollection(\MongoCollection $collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    
    /**
     * {@inheritdoc}
     * 
     * Maps an array entry with the key ['_id'] to ['id'] with
     * casting to string. (To deal with MongoId-Objects.)
     * If <code>$data['id']</code> is set, it has higher priority.
     * 
     * @param array $data
     */
    public function create(array $data=array())
    {
        if (isset($data['_id'])) {
            $data['id'] = isset($data['id']) ? $data['id'] : (string) $data['_id'];
            unset($data['_id']);
        }
        return parent::create($data);
    }
    
    /**
     * {@inheritdoc} 
     * @see \Core\Mapper\MongoDb\MapperInterface::getCollection()
     */
    public function getCollection()
    {
        return $this->_collection;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param string|\MongoId $id Mongodb id
     */
    public function find($id, $fields = array())
    {
        if (!$id instanceOf \MongoId) {
            $id = $this->_getMongoId($id);
        }
        $data = $this->_collection->findOne(array('_id' => $id), $fields);
        return $data;
        return $this->_createFromResult($data);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param CriteriaInterface|null $criteria
     * @return Collection
     */
    public function fetchAll($criteria=null)
    {
        if (null === $criteria) {
            $criteria = new \Core\Mapper\Query\Query();
        }
        //$conv = new \Core\Mapper\MongoDb\QueryConverter($criteria);
        $cursor = $this->convertQuery($criteria);
        return $this->_createCollectionFromResult($cursor);
    }
    
    /**
     * Saves an application
     *
     * @param ModelInterface $model
     * @see \Core\Mapper\MapperInterface::save()
     */
    public function save(ModelInterface $model)
    {
        
        $hydrator = $this->getModelHydrator();
        $data = $hydrator->extract($model);
  
        print_r($data);
        return;
        if ($data['id']) {
            $data['_id'] = $this->_getMongoId($data['id']);
        }
        unset($data['id']);
    
        $this->_collection->save($data);
        $model->setId((string) $data['_id']);
    }
    
    /**
     * Creates a model from a Mongo-Query-Result.
     * 
     * If the result is NULL, no model will be created and 
     * null is returned instead.
     * 
     * @param array|null $data
     * @return \Core\Model\ModelInterface|null
     * @uses create()
     */
    protected function _createFromResult($data)
    {
        return $data ? $this->create($data) : null;
    }
    
    protected function _createCollectionFromResult($cursor)
    {
        $models = array();
        foreach ($cursor as $data) {
            $models[] = $data; //$this->create($data);
        }
        $collection = $this->createCollection($models);
        return $collection;
    }
    
    /**
     * Creates a MongoId-Object from a string.
     * 
     * @param string $id
     * @return \MongoId
     */
    protected function _getMongoId($id)
    {
        return new \MongoId($id);
    }
    
    
    
}