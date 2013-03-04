<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb */
namespace Auth\Mapper\MongoDb;

use Core\Mapper\MongoDb\AbstractMapper;
use Auth\Model\UserModel;
use Core\Model\ModelInterface;
use Auth\Mapper\UserMapperInterface;

/**
 * User mapper factory
 */
class UserMapper extends AbstractMapper implements UserMapperInterface
{
    /**
     * {@inheritdoc}
     * @see \Auth\Mapper\UserMapperInterface::findByEmail()
     */
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
    
    /**
     * Saves a user
     * 
     * @param ModelInterface $model
     */
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
        } else {
            $this->_collection->insert($data);
        }
    }
}