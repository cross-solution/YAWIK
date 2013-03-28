<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb */
namespace Applications\Mapper\MongoDb;

use Core\Mapper\MongoDb\AbstractMapper;
use Core\Model\ModelInterface;


/**
 * User mapper factory
 */
class ApplicationMapper extends AbstractMapper
{
    
    
    /**
     * Saves an application
     * 
     * @param ModelInterface $model
     * @see \Core\Mapper\MapperInterface::save()
     */
    public function save(ModelInterface $model)
    { 
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods();
        $data = $hydrator->extract($model);
        unset($data['id']);
        
        if ( ($id = $model->getId()) ) {
            $data['_id'] = $this->_getMongoId($id);
        }
        
        $this->_collection->save($data);
        $model->setId((string) $data['_id']);
    }
}