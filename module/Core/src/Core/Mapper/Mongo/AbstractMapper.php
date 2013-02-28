<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Mapper\Mongo;

use Core\Mapper\Mongo\MapperInterface;


/**
 *
 * 
 */
class AbstractMapper implements MapperInterface
{
    protected $_db;
    protected $_collection;
    
    public function setDatabase(\MongoDD $database)
    {
        $this->_db = $db;
        if (null !== $this->_collection) {
            $this->_collection = $db->{$this->_collection};
        }
        return $this;
    }
    
    public function getDatabase()
    {
        return $this->_db;
    }
    
    public function setCollection($collection)
    {
        $this->_collection=$db->{$collection};
        return $this;
    }

    public function getCollection()
    {
        return $this->_collection;
    }
    
}