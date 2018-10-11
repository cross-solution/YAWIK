<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Organizations\Repository;

use Auth\Entity\UserInterface;
use Core\Repository\AbstractRepository;
use Organizations\Entity\EmployeeInterface;
use Organizations\Entity\OrganizationInterface;

/**
 * This is the repository for Organizations entities.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @todo   write test
 */
class Organization extends AbstractRepository
{
    /**
     * Gets a cursor to a set of organizations
     *
     * @param array $params
     */
    public function getPaginatorCursor($params)
    {
        return $this->getPaginationQueryBuilder($params)
                    ->getQuery()
                    ->execute();
    }

    /**
     * Gets a query builder to search for organizations
     *
     * @param array $params
     * @return mixed
     */
    protected function getPaginationQueryBuilder($params)
    {
        $filter = $this->getService('filterManager')->get('Organizations/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
        
        return $qb;
    }

    /**
     * Gets a cursor for all hiring organizations.
     *
     * @param OrganizationInterface $organization parent organization
     *
     * @return \Doctrine\ODM\MongoDB\Cursor
     * @usedBy \Organizations\Entity\Organization::getHiringOrganizations()
     * @since 0.18
     */
    public function getHiringOrganizationsCursor(OrganizationInterface $organization)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('parent')->equals($organization->getId());
        $qb->field('isDraft')->equals(false);
        $q  = $qb->getQuery();
        $cursor = $q->execute();

        return $cursor;
    }

    /**
     * Find a organizations by an name
     *
     * @param String $name
     * @param boolean $create
     * @return array
     */
    public function findByName($name, $create = true)
    {
        $query = $this->dm->createQueryBuilder('Organizations\Entity\OrganizationName')->hydrate(false)->field('name')->equals($name)->select("_id");
        $result = $query->getQuery()->execute()->toArray(false);
        if (empty($result) && $create) {
            $repositoryNames = $this->dm->getRepository('Organizations\Entity\OrganizationName');
            $entityName = $repositoryNames->create();
            $entityName->setName($name);
            $entity = $this->create();
            $entity->setOrganizationName($entityName);
            $this->store($entity);
            $organizations = array($entity);
        } else {
            $idName = $result[0]['_id'];
            $organizations = $this->findBy(array('organizationName' => $idName));
        }
        return $organizations;
    }
    
    public function findbyRef($ref, $create = true)
    {
        $entity = $this->findOneBy(array('externalId' => $ref));
        if (empty($entity)) {
            $entity = $this->create();
            $entity->setExternalId($ref);
        }
        return $entity;
    }

    /**
     * Finds the main organization of an user.
     *
     * @param string|UserInterface $userOrId
     *
     * @return null|OrganizationInterface
     */
    public function findByUser($userOrId)
    {
        $userId = $userOrId instanceof \Auth\Entity\UserInterface ? $userOrId->getId() : $userOrId;
        $qb     = $this->createQueryBuilder();

        /*
         * HiringOrganizations also could be associated to the user, but we
         * do not want them to be queried here, so the query needs to check the
         * "parent" field, too.
         */
//        $qb->addAnd(
//           $qb->expr()->field('user')->equals($userId)
//                      ->addOr(
//                            $qb->expr()->addOr($qb->expr()->field('parent')->exists(false))
//                                       ->addOr($qb->expr()->field('parent')->equals(null))
//           )
//        );
        $qb->addAnd($qb->expr()->field('user')->equals($userId))
           ->addAnd(
               $qb->expr()->addOr($qb->expr()->field('parent')->exists(false))
                               ->addOr($qb->expr()->field('parent')->equals(null))
           );

        $q      = $qb->getQuery();
        $entity = $q->getSingleResult();

        return $entity;
    }

    /**
     * Finds the organization, an user is employed by.
     *
     * @param string|UserInterface $userOrId
     *
     * @return null|OrganizationInterface
     */
    public function findByEmployee($userOrId)
    {
        $userId = $userOrId instanceof \Auth\Entity\UserInterface ? $userOrId->getId() : $userOrId;

        /*
         * Employees collection is only set on main organization,
         * so here, we do not have to query the "parent" field.
         *
         * Only search for "assigned" employees.
         */
        $entity = $this->findOneBy(
            array(
            'employees.user' => new \MongoId($userId),
            'employees.status' => EmployeeInterface::STATUS_ASSIGNED
            )
        );

        return $entity;
    }

    public function findPendingOrganizationsByEmployee($userOrId)
    {
        $userId = $userOrId instanceof \Auth\Entity\UserInterface ? $userOrId->getId() : $userOrId;

        $collection = $this->findBy(
            array(
            'employees.user' => new \MongoId($userId),
            'employees.status' => EmployeeInterface::STATUS_PENDING
            )
        );

        return $collection;
    }

    public function getEmployersCursor(UserInterface $user)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('refs.employees')->equals($user->getId());

        $q  = $qb->getQuery();
        $c  = $q->execute();

        return $c;
    }
	
	/**
	 * @param array|null    $data
	 * @param bool          $persist
	 * @return \Organizations\Entity\Organization
	 */
    public function create(array $data = null, $persist=false)
    {
        $entity = parent::create($data);
        $entity->isDraft(true);
        return $entity;
    }

    /**
     * creates a new Organization, no matter if a organization with this name already exists,
     * also creates a new Name, but link this Name to another OrganizationName-Link, if this Name already exists
     * @param $name
     */
    public function createWithName($name)
    {
        $entity = parent::create();
        $repositoryNames = $this->dm->getRepository('Organizations\Entity\OrganizationName');
        $entityName = $repositoryNames->create();
        $entityName->setName($name);
        $entity->setOrganizationName($entityName);
        return $entity;
    }

    /**
     * Look for an drafted Document of a given user
     *
     * @param $user
     * @return \Organizations\Entity\Organization|null
     */
    public function findDraft($user)
    {
        if ($user instanceof UserInterface) {
            $user = $user->getId();
        }

        $document = $this->findOneBy(
            array(
            'isDraft' => true,
            'user' => $user
            )
        );

        if (!empty($document)) {
            return $document;
        }

        return null;
    }
    
    /**
     * Get organizations for given user ID
     *
     * @param string $userId
     * @param int $limit
     * @return Cursor
     * @since 0.27
     */
    public function getUserOrganizations($userId, $limit = null)
    {
        $qb = $this->createQueryBuilder(null)
            ->field('user')->equals($userId)
            ->sort(['DateCreated.date' => -1]);
    
        if (isset($limit)) {
            $qb->limit($limit);
        }
    
        return $qb->getQuery()->execute();
    }
}
