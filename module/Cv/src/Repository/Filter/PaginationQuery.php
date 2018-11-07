<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PaginationQuery.php */
namespace Cv\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Auth\Entity\UserInterface;
use Cv\Entity\Status;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

class PaginationQuery extends AbstractPaginationQuery
{
    protected $repositoryName = 'Cv/Cv';
    protected $user;
    
    public function __construct(UserInterface $user = null)
    {
        $this->user = $user;
    }

    /**
     * @param   array $params
     * @param   QueryBuilder $queryBuilder
     * @return  mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        if (isset($params['search']) && !empty($params['search'])) {
            $search = strtolower($params['search']);
            $expr = $queryBuilder->expr()->operator('$text', ['$search' => $search]);
            $queryBuilder->field(null)->equals($expr->getQuery());
        }

        if (isset($params['l']) && $params['l'] instanceof \Core\Entity\AbstractLocation) {
            $location = $params['l']; /* @var \Core\Entity\LocationInterface $location */
            if ($location->getCoordinates()) {
                $coordinates = $location->getCoordinates()->getCoordinates();
                $queryBuilder->field('preferredJob.desiredLocations.coordinates')->geoWithinCenter(
                    $coordinates[0],
                    $coordinates[1],
                    (float) $params['d'] / 100
                );
            }
        }

        $queryBuilder->addOr($queryBuilder->expr()->field('permissions.view')->equals($this->user->getId()))
            ->addOr($queryBuilder->expr()->field('status.name')->equals(Status::PUBLIC_TO_ALL));


        return $queryBuilder;
    }
}
