<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Mapper\MongoDb;

use Core\Mapper\MongoDb\MapperInterface;
use Core\Mapper\AbstractMapper as CoreAbstractMapper;


/**
 *
 * 
 */
abstract class AbstractMapper extends CoreAbstractMapper implements MapperInterface
{
    protected $_collection;
    
    public function setCollection(\MongoCollection $collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection()
    {
        return $this->_collection;
    }
    
    public function find($id)
    {
        if (!$id instanceOf \MongoId) {
            $id = $this->_getMongoId($id);
        }
        return $this->_collection->findOne(array('_id' => $id));
    }
    
    protected function _getMongoId($id)
    {
        return new \MongoId($id);
    }
}