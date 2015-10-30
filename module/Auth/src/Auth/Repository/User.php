<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } elseif (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        return parent::findBy($criteria, $sort, $limit, $skip);
    }

    /**
     * {@inheritDoc}
     * @return null | UserInterface
     */
    public function findOneBy(array $criteria)
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } elseif (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        return parent::findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function createQueryBuilder($findDrafts = false)
    {
        $qb = parent::createQueryBuilder();
        if (null !== $findDrafts) {
            $qb->field('isDraft')->equals($findDrafts);
        }
        return $qb;
    }

    /**
     * Creates a User
     *
     * @see \Core\Repository\AbstractRepository::create()
     * @return UserInterface
     */
    public function create(array $data = null)
    {
        $entity = parent::create($data);
        
        $eventArgs = new LifecycleEventArgs($entity, $this->dm);
        $this->dm->getEventManager()->dispatchEvent(
            Events::postLoad,
            $eventArgs
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
    public function findByLogin($login)
    {
        $entity = $this->findOneBy(array('login' => $login));
        return $entity;
    }

    /**
     * @param      $email
     * @param bool $isDraft
     *
     * @return UserInterface|null
     */
    public function findByEmail($email, $isDraft = false)
    {
        $entity = $this->findOneBy(
            array(
            '$or' => array(
                array('email' => $email),
                array('info.email' => $email),
            ),
            'isDraft' => $isDraft,
            )
        );

        return $entity;
    }

    /**
     * Finds user by login name or email
     *
     * @param string $identity
     * @param string $suffix
     *
     * @return UserInterface|null
     */
    public function findByLoginOrEmail($identity, $suffix = '')
    {
        return $this->findOneBy(
            array(
            '$or' => array(
                array('login' => $identity . $suffix),
                array('info.email' => $identity)
            )
            )
        );
    }

    /**
     * Find an user by a token hash.
     *
     * @param string $tokenHash
     *
     * @return UserInterface|null
     */
    public function findByToken($tokenHash)
    {
        $criteria = array(
            'isDraft' => null,
            'tokens.hash' => $tokenHash
        );

        return $this->findOneBy($criteria);
    }
    
    /**
     * Finds user by internal id
     *
     * @param array $ids
     * @return \MongoCursor
     */
    public function findByIds(array $ids)
    {
        return $this->findBy(
            array(
            '_id' => array('$in' => $ids)
            )
        );
    }
    
    /**
     * Find user by query
     *
     * @param String $query
     * @deprecated since 0.19 not used anymore and probably broken.
     * @return object
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
    public function copyUserInfo(Info $info)
    {
        $contact = new Info();
        $contact->fromArray(Info::toArray($info));
    }
}
