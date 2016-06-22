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

        if (isset($params['location']) && isset($params['location']->coordinates)) {
            $coordinates = $params['location']->coordinates->getCoordinates();
            $queryBuilder->field('preferredJob.desiredLocations.coordinates')->geoWithinCenter(
                $coordinates[0],
                $coordinates[1],
                (float)$params['d'] / 100
            );
        }


        return $queryBuilder;

    }
}
