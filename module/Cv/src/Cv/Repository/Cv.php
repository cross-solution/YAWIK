<?php

namespace Cv\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\AbstractRepository;
use Auth\Entity\UserInterface;

/**
 * class for accessing CVs
 */
class Cv extends AbstractRepository
{
    /**
     * Gets a pointer to access a CV
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
     * Gets a query builder to search for CVs
     *
     * @param array $params
     * @return unknown
     */
    protected function getPaginationQueryBuilder($params)
    {
        $filter = $this->getService('filterManager')->get('Applications/PaginationQuery');
        $qb = $filter->filter($params, $this->createQueryBuilder());
    
        return $qb;
    }

    /**
     * Look for an drafted Document of a given user
     *
     * @param $user
     * @return \Cv\Entity\Cv|null
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
}
