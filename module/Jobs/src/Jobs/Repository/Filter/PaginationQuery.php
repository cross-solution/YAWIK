<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
        $user = $this->auth->getUser();
        if ($user->getRole()=='recruiter') {
            /*
             * a recruiter can see his jobs and jobs from users who gave permissions to do so
             */
            $user = $this->auth->getUser();
            if (isset($value['by'])) {
                switch ($value['by']) {
                    case 'me':
                    default:
                        $queryBuilder->field('user')->equals($user->id);
                        break;
                        
                    case 'all':
                        $queryBuilder->field('permissions.view')->equals($user->id);
                        break;
                }
            }
            if (isset($value['status']) && !empty($value['status'])) {
                $queryBuilder->field('status')->equals((string) $value['status']);
            }
            
        } else  {
            /*
             * an applicants or guests can see all aktive jobs
             */
            $queryBuilder->field('status')->equals('active');
        }
    
        
        
        /*
         * search jobs by keywords
         */
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