<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Applications\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use Zend\Stdlib\Parameters;

/**
 * maps query parameters to entity attributes
 *
 * @package Applications
 */
class PaginationQuery extends AbstractPaginationQuery
{
    /**
     * Repository to query
     *
     * @var String
     */
    protected $repositoryName="Applications/Application";
    
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
     * Creates a query for filtering applications
     * @see \Core\Repository\Filter\AbstractPaginationQuery::createQuery()
     * @param array $params
     * @param $queryBuilder
     */
    public function createQuery($params, $queryBuilder)
    {
        $userID = $this->auth->getUser()->getId();
        if ($params instanceof Parameters) {
            $value = $params->toArray();
        } else {
            $value = $params;
        }
    
         
        if (isset($value['by']) && 'me' == $value['by']) {
            #            $queryBuilder->field('user')->equals($this->auth->getUser()->id);
        }
        if (isset($value['by']) && 'new' == $value['by']) {
            #             $queryBuilder->field('readBy')->notEqual( new \MongoId($this->auth->getUser()->id));
        }
        if (isset($value['job']) && !empty($value['job'])) {
            $queryBuilder->field('job')->equals($value['job']);
        }
        
        
        if (isset($value['unread']) && !empty($value['unread'])) {
            $queryBuilder->field('readBy')->notEqual($userID);
        }
        
    
        if (isset($value['search']) && !empty($value['search'])) {
            $search = strtolower($value['search']);
            $searchPatterns = array();
    
            foreach (explode(' ', $search) as $searchItem) {
                $searchPatterns[] = new \MongoRegex('/^' . $searchItem . '/');
            }
            $queryBuilder->field('keywords')->all($searchPatterns);
        }
        
        /*
         * We only show applications to which the user has view permissions.
         */
        $queryBuilder->field('permissions.view')->equals($userID);

        if (!isset($value['sort'])) {
            $value['sort'] = '-date';
        }
        
        if (isset($value['status']) && 'all' != $value['status']) {
            $queryBuilder->field('status.name')->equals($value['status']);
        }
        $queryBuilder->sort($this->filterSort($value['sort']));
        
        return $queryBuilder;
    }
}
