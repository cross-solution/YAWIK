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

use Core\Repository\Mapper\AbstractBuilderAwareMapper as CoreMapper;
use Core\Entity\EntityInterface;


/**
 * User mapper factory
 */
class UserMapper extends CoreMapper
{
    
    public function create($data = null)
    {
        if (null === $data) {
            $data = Array();
        }
        return $this->buildEntity($data, 'user');
        
    }
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
     *
     * @param string|\MongoId $id Mongodb id
     */
    public function find($id, array $fields = array(), $exclude = false)
    {
        $data = $this->getData($id, $fields, $exclude);
        $builder = $this->builders->get('user');
        $entity = $builder->build($data);
        return $entity;
    }
    
    /**
     * {@inheritdoc}
     * @see \Auth\Mapper\UserMapperInterface::findByProfileIdentifier()
     */
    public function findByProfileIdentifier($identifier)
    {
        $data = $this->getData(array('profile.identifier' => $identifier));
        $builder = $this->getBuilder('user');
        $entity = $builder->build($data);
        return $entity;
    }
    
    public function findByLogin($login)
    {
        $data = $this->getData(array('login' => $login), array('info'), true);
        if (null === $data) { return null; }
        $builder = $this->getBuilder('user');
        $entity = $builder->build($data);
        return $entity;
    }
    
    /**
     * {@inheritdoc}
     *
     * @param CriteriaInterface|null $criteria
     * @return Collection
     */
    public function fetch(array $query = array(), array $fields = array(), $exclude = false)
    {
        $cursor     = $this->getCursor($query, $fields, $exclude);
        $builder    = $this->builders->get('user');
        $collection = $builder->buildCollection($cursor);
        return $collection;
    }
    
    public function save(EntityInterface $entity)
    {
        $builder = $this->builders->get('user');
        $data    = $builder->unbuild($entity);
        $id      = $this->saveData($data);
        if ($id) {
            $entity->setId($id);
        }
    
    }
    
}