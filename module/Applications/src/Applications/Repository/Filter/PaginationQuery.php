<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
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
        if ($params instanceOf Parameters) {
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
        
        
    
        if (isset($value['search']) && !empty($value['search'])) {
            $search = strtolower($value['search']);
            $searchPatterns = array();
    
            foreach (explode(' ', $search) as $searchItem) {
                $searchPatterns[] = new \MongoRegex('/^' . $searchItem . '/');
            }
            $queryBuilder->field('keywords')->all($searchPatterns);
        }
        
        if ($this->auth->getUser()->getRole()=='recruiter') {
            /*
             * a recruiter can see applications, which are related to his jobs
            */
//             if (isset($value['by']) && 'new' === $value['by']) {
//                 $properties['readBy'] = array('$ne' => $this->auth->getUser()->id);
//             }
//             $queryBuilder->field('refs.jobs.userId')->equals($this->auth->getUser()->id);

            /*
             * Recruiter sees all application to which he has view permission.
             * 
             */
            $queryBuilder->field('permissions.view')->equals($this->auth->getUser()->getId());

        } else {
            /*
             * an applicant can see his own applications
            */
            $queryBuilder->field('refs.users.id')->equals($this->auth->getUser()->id);
        }
    

        if (!isset($value['sort'])) {
            $value['sort'] = '-date';
        }
        $queryBuilder->sort($this->filterSort($value['sort']));
        
        return $queryBuilder;
    }   
}
?>