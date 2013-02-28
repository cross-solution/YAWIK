<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Mapper\Mongo;


use Core\Mapper\Mongo\AbstractMapper;
use Auth\Model\UserModel;
use Core\Model\ModelInterface;

/**
 *
 */
class UserMapper extends AbstractMapper
{
    protected $_collection = 'users';
    
    public function find($id)
    {
        
    }
    
    public function findByEmail($email)
    {
        return $this->_collection->findOne(array('email' => $email));
    }
    
    public function save(ModelInterface $model)
    { 
        $data = array(
            'email' => $model->email,
        );
        if ($model->getId()) {
            $data['_id'] = $this->_getMongoId($model->getId());
            $this->_collection->update(array('_id' => $data['_id']), $data);
            return;
        }
        $this->_collection->insert($data);
    }
}