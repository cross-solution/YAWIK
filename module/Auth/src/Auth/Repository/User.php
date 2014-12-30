<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Repository;

use \Auth\Entity\Info;
use Auth\Entity\UserInterface;
use Core\Repository\AbstractRepository;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;

/**
 * class for accessing a user 
 */
class User extends AbstractRepository
{
    /**
     * Creates a User
     * 
     * @see \Core\Repository\AbstractRepository::create()
     * @return UserInterface
     */
    public function create(array $data=null)
    {
        $entity = parent::create($data);
        
        $eventArgs = new LifecycleEventArgs($entity, $this->dm);
        $this->dm->getEventManager()->dispatchEvent(
            Events::postLoad, $eventArgs
        );
        return $entity;
    }
    
    /**
     * Finds user by profile identifier
     * 
     * @param String $identifier
     * @return UserInterface
     */
    public function findByProfileIdentifier($identifier)
    {
        $entity = $this->findOneBy(array('profile.identifier' => $identifier));
        return $entity;
    }
    
    /**
     * Finds user by login name
     * 
     * @param string $login
     * @return UserInterface
     */
    public function findByLogin($login) {
        $entity = $this->findOneBy(array('login' => $login));
        return $entity;
    }

    /**
     * Finds user by login name or email
     *
     * @param string $identity
     *
     * @return UserInterface|null
     */
    public function findByLoginOrEmail($identity)
    {
        $qb = $this->createQueryBuilder();
        $qb->addOr($qb->expr()->field('login')->equals($identity))
            ->addOr($qb->expr()->field('info.email')->equals($identity));

        return $qb->getQuery()->getSingleResult();
    }
    
    /**
     * Finds user by internal id
     * 
     * @param array $ids
     */
    public function findByIds(array $ids)
    {
        return $this->findBy(array(
            '_id' => array('$in' => $ids)
        ));
    }
    
    /**
     * Find user by query
     * 
     * @param String $query
     */
    public function findByQuery($query)
    {
        $qb = $this->createQueryBuilder();
        $parts  = explode(' ', trim($query));
        
        foreach ($parts as $q) {
            $regex = new \MongoRegex('/^' . $query . '/i');
            $qb->addOr($qb->expr()->field('info.firstName')->equals($regex));
            $qb->addOr($qb->expr()->field('info.lastName')->equals($regex));
            $qb->addOr($qb->expr()->field('info.email')->equals($regex));
        } 
        $qb->sort(array('info.lastName' => 1))
           ->sort(array('info.email' => 1));
        
        return $qb->getQuery()->execute();
    }
    
    /**
     * Copy user info into the applications info Entity
     * 
     * @param \Auth\Entity\Info $info
     */
    public function copyUserInfo(Info $info){
        $contact = new Info();
        $contact->fromArray(Info::toArray($info));
    }
}