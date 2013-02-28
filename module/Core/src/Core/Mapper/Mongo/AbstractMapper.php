<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Mapper\Mongo;

use Core\Mapper\Mongo\MapperInterface;
use Core\Mapper\AbstractMapper as CoreAbstractMapper;


/**
 *
 * 
 */
abstract class AbstractMapper extends CoreAbstractMapper implements MapperInterface
{
    protected $_db;
    protected $_collection;
    
    public function setDatabase(\MongoDB $database)
    {
        $this->_db = $database;
        if (null !== $this->_collection) {
            $this->_collection = $database->{$this->_collection};
        }
        return $this;
    }
    
    public function getDatabase()
    {
        return $this->_db;
    }
    
    public function setCollection($collection)
    {
        $this->_collection=$this->_db->{$collection};
        return $this;
    }

    public function getCollection()
    {
        return $this->_collection;
    }
    
    public function find($id)
    {
        $mongoId = $this->_getMongoId($id);
        
    }
    
    protected function _getMongoId($id)
    {
        return new \MongoId($id);
    }
}