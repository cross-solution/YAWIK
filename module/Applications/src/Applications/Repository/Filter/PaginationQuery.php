<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Applications\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;

/**
 * maps query parameters to entity attributes
 * 
 * @author cbleek
 *
 */
class PaginationQuery extends AbstractPaginationQuery 
{
    
    protected $repositoryName="Applications/Application";
    
    public function __construct($auth)
    {
        $this->auth = $auth;
    }
    
    public function createQuery($params, $queryBuilder)
    {
        $value = $params->toArray();
    
        if (isset($value['sort'])) {
            $queryBuilder->sort($this->filterSort($value['sort']));
        }
         
        if (isset($value['by']) && 'me' == $value['by']) {
            $queryBuilder->field('userId')->equals($this->auth->getUser()->id);
        }
    
        if (isset($value['search']) && !empty($value['search'])) {
            $search = strtolower($value['search']);
            $searchPatterns = array();
    
            foreach (explode(' ', $search) as $searchItem) {
                $searchPatterns[] = new \MongoRegex('/^' . $searchItem . '/');
            }
            $queryBuilder->field('keywords')->all($searchPatterns);
        }
    
        return $queryBuilder;
    }
    
    protected function filterSort($sort)
    {
        if ('-' == $sort{0}) {
            $sortProp = substr($sort, 1);
            $sortDir  = -1;
        } else {
            $sortProp = $sort;
            $sortDir = 1;
        }
        switch ($sortProp) {
            case "date":
                $sortProp = "datePublishStart.date";
                break;
    
            default:
                break;
        }
    
        return array($sortProp => $sortDir);
    }
}

?>