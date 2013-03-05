<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core MongoDb Mappers */
namespace Core\Mapper\MongoDb;

use Core\Mapper\MongoDb\MapperInterface;
use Core\Mapper\AbstractMapper as CoreAbstractMapper;

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
    public function find($id)
    {
        if (!$id instanceOf \MongoId) {
            $id = $this->_getMongoId($id);
        }
        $data = $this->_collection->findOne(array('_id' => $id));
        return $this->create($data);
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