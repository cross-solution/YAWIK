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
        return $this->_collection->findOne(array('_id' => $id));
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