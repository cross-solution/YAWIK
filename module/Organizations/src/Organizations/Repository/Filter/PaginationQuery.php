<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Organizations\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Zend\Stdlib\Parameters;

/**
 * maps query parameters to entity attributes
 *
 * @package Organizations
 * @author Mathias Weitz <weitz@cross-solution.de>
 */
class PaginationQuery extends AbstractPaginationQuery
{
    /**
     * Repository to query
     *
     * @var String
     */
    protected $repositoryName="Organizations/Organization";
    
    /**
     * Sortable fields
     *
     * @var array
     */
    protected $sortPropertiesMap = array(
        'date' => 'dateCreated.date',
    );
    
    /**
     * Constructs pagination query.
     *
     * @param \Auth\AuthenticationService $auth
     */
    public function __construct($auth)
    {
        $this->auth = $auth;
    }
    
    /**
     * Creates a query for filtering organizations
     *
     * @see \Core\Repository\Filter\AbstractPaginationQuery::createQuery()
     * @param $params
     * @param \Doctrine\ODM\MongoDB\Query\Builder $queryBuilder
     * @return mixed
     */
    public function createQuery($params, $queryBuilder)
    {
        $userID = $this->auth->getUser()->getId();
        if ($params instanceof Parameters) {
            $value = $params->toArray();
        } else {
            $value = $params;
        }

        
        /*
         * We only show organizations to which the user has view permissions.
         */
        $queryBuilder->field('permissions.view')->equals($userID);

        if (!isset($value['sort'])) {
            $value['sort'] = '-date';
        }

        $queryBuilder->sort($this->filterSort($value['sort']));
        
        return $queryBuilder;
    }
}
