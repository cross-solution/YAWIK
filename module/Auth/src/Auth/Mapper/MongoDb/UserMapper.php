<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Auth\Mapper\MongoDb;


use Core\Mapper\MongoDb\AbstractMapper;
use Auth\Model\UserModel;
use Core\Model\ModelInterface;
use Auth\Mapper\UserMapperInterface;

/**
 *
 */
class UserMapper extends AbstractMapper implements UserMapperInterface
{
    
    public function findByEmail($email)
    {
        $data = $this->_collection->findOne(array('email' => $email));
        if (null === $data) {
            return null;
        }
        $data['id'] = (String) $data['_id'];
        unset ($data['_id']);
        return $this->create($data);
    }
    
    public function save(ModelInterface $model)
    { 
        $data = array(
            'email' => $model->email,
            'firstName' => $model->firstName,
            'lastName' => $model->lastName,
            'displayName' => $model->displayName,
        );
        if (!empty($model->facebookInfo)) {
            $data['facebookInfo'] = $model->facebookInfo;
        }
        if (!empty($model->linkedInInfo)) {
            $data['linkedInInfo'] = $model->linkedInInfo;
        }
        if (!empty($model->xingInfo)) {
            $data['xingInfo'] = $model->xingInfo;
        }
        if ($model->getId()) {
            $data['_id'] = $this->_getMongoId($model->getId());
            $this->_collection->update(array('_id' => $data['_id']), $data);
            return;
        }
        $this->_collection->insert($data);
    }
}