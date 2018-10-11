<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Repository;

use \Auth\Entity\Info;
use Auth\Entity\UserInterface;
use Core\Repository\AbstractRepository;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Auth\Exception\UserDeactivatedException;

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
        
        if (!array_key_exists('status.name', $criteria)) {
            $criteria['status.name'] = \Jobs\Entity\StatusInterface::ACTIVE;
        } elseif (null === $criteria['status.name']) {
            unset($criteria['status.name']);
        }
        
        return parent::findBy($criteria, $sort, $limit, $skip);
    }
    
    /**
     * Finds a document by its identifier
     *
     * @param string|object $id The identifier
     * @param int $lockMode
     * @param int $lockVersion
     * @param array $options
     * @throws Mapping\MappingException
     * @throws LockException
     * @throws UserDeactivatedException
     * @return null | UserInterface
     */
    public function find($id, $lockMode = \Doctrine\ODM\MongoDB\LockMode::NONE, $lockVersion = null, array $options = [])
    {
        return $this->assertEntity(parent::find($id, $lockMode, $lockVersion), $options);
    }

    /**
     * @param array $criteria
     * @param array $options
     * @throws UserDeactivatedException
     * @return null | UserInterface
     */
    public function findOneBy(array $criteria, array $options = [])
    {
        if (!array_key_exists('isDraft', $criteria)) {
            $criteria['isDraft'] = false;
        } elseif (null === $criteria['isDraft']) {
            unset($criteria['isDraft']);
        }
        return $this->assertEntity(parent::findOneBy($criteria), $options);
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
    public function create(array $data = null, $persist=false)
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
     * @param string $identifier
     * @param string $provider
     * @param array $options
     * @return UserInterface
     */
    public function findByProfileIdentifier($identifier, $provider, array $options = [])
    {
        return $this->findOneBy(array('profiles.' . $provider . '.auth.identifier' => $identifier), $options) ?: $this->findOneBy(array('profile.identifier' => $identifier), $options);
    }
    
    /**
     * Returns true if profile is already assigned to anotherUser
     *
     * @param int $curentUserId
     * @param string $identifier
     * @param string $provider
     * @return bool
     */
    public function isProfileAssignedToAnotherUser($curentUserId, $identifier, $provider)
    {
        $qb = $this->createQueryBuilder(null);
        $qb->field('_id')->notEqual($curentUserId)
            ->addAnd(
                $qb->expr()
                    ->addOr($qb->expr()->field('profiles.' . $provider . '.auth.identifier' )->equals($identifier))
                    ->addOr($qb->expr()->field('profile.identifier')->equals($identifier))
            );
        
        return $qb->count()
            ->getQuery()
            ->execute() > 0;
    }
    
    /**
     * Finds user by login name
     *
     * @param string $login
     * @param array $options
     * @return UserInterface
     */
    public function findByLogin($login, array $options = [])
    {
        $entity = $this->findOneBy(array('login' => $login), $options);
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
    
    /**
     * @param UserInterface $user
     * @param array $options
     * @throws UserDeactivatedException
     * @return null | UserInterface
     */
    protected function assertEntity(UserInterface $user = null, array $options)
    {
        if (isset($user) && (!isset($options['allowDeactivated']) || !$options['allowDeactivated']) && !$user->isActive())
        {
            throw new UserDeactivatedException(sprintf('User with ID %s is not active', $user->getId()));
        }
        
        return $user;
    }
}
