<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Jobs\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;

/**
 * maps query parameters to entity attributes
 * 
 * @author cbleek
 *
 */
class PaginationQuery extends AbstractPaginationQuery 
{
    
    protected $repositoryName="Jobs/Job";
    
    public function __construct($auth)
    {
        $this->auth = $auth;
    }
    
    public function createQuery($params, $queryBuilder)
    {
        $value = $params->toArray();
         
        
        if ($this->auth->getUser()->getRole()=='recruiter') {
            /*
             * a recruiter can see his jobs
             */
            $queryBuilder->field('user')->equals($this->auth->getUser()->id);
        } else {
            /*
             * an applicant can see all aktive jobs
            */
            $queryBuilder->field('refs.users.id')->equals($this->auth->getUser()->id);
        }
    
        if (isset($value['by']) && 'me' == $value['by']) {
            $queryBuilder->field('user')->equals($this->auth->getUser()->id);
        }
        
        if (isset($value['status'])) {
            $queryBuilder->field('status')->equals((string) $value['status']);
        }
        
    
        if (isset($value['search']) && !empty($value['search'])) {
            $search = strtolower($value['search']);
            $searchPatterns = array();
    
            foreach (explode(' ', $search) as $searchItem) {
                $searchPatterns[] = new \MongoRegex('/^' . $searchItem . '/');
            }
            $queryBuilder->field('keywords')->all($searchPatterns);
        }

        if (isset($value['sort'])) {
            foreach(explode(",",$value['sort']) as $sort) {
                $queryBuilder->sort($this->filterSort($sort));
            }
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
            case "title":
                $sortProp = "title";
                break;
            case "cam":
                $sortProp = "camEnabled";
                break;
    
            default:
                break;
        }
    
        return array($sortProp => $sortDir);
    }
    
    
    
}

?>