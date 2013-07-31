<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb */
namespace Auth\Repository\Mapper;

use Core\Repository\Mapper\AbstractMapper;


/**
 * User mapper factory
 */
class UserMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     * @see \Auth\Mapper\UserMapperInterface::findByEmail()
     */
    public function findByEmail($email)
    {
        $data = $this->getCollection()->findOne(array('email' => $email));
        return $data;
    }
    
    /**
     * {@inheritdoc}
     * @see \Auth\Mapper\UserMapperInterface::findByProfileIdentifier()
     */
    public function findByProfileIdentifier($identifier)
    {
        $data = $this->getCollection()->findOne(array('profile.identifier' => $identifier));
        return $data;
    }
    
    public function findByDisplayName($displayName)
    {
        $data = $this->getCollection()->findOne(array('displayName' => $displayName));
        return $data;
    }
    
}