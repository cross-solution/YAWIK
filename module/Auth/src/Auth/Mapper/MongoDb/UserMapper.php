<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
        return $this->_createFromResult($data);
    }
    
    /**
     * {@inheritdoc}
     * @see \Auth\Mapper\UserMapperInterface::findByProfileIdentifier()
     */
    public function findByProfileIdentifier($identifier)
    {
        $data = $this->_collection->findOne(array('profile.identifier' => $identifier));
        return $this->_createFromResult($data);
    }
    
    /**
     * Saves a user
     * 
     * @param ModelInterface $model
     * @see \Core\Mapper\MapperInterface::save()
     */
    public function save(ModelInterface $model)
    { 
    	$data = array(
            'email' => $model->email,
            'firstName' => $model->firstName,
            'lastName' => $model->lastName,
            'displayName' => $model->displayName,
            'birthDay' => $model->birthDay,
        	'birthMonth' => $model->birthMonth,
        	'birthYear' => $model->birthYear,
        	'gender' => $model->gender,	
        	'street' => $model->street,
        );
        if (!empty($model->profile)) {
            $data['profile'] = $model->profile;
        }
        
        if ($model->getId()) {
            $data['_id'] = $this->_getMongoId($model->getId());
        }
        
        $this->_collection->save($data);
        $model->setId((string) $data['_id']);
    }
}