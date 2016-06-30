<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Filter;


class JobBoardPaginationQuery extends AbstractPaginationQuery
{
    /**
     * @var array
     */
    protected $sortPropertiesMap = [
        'company' => 'companyName',
        'date'    => 'dateCreated',
    ];

    /**
     * @inheritdoc
     */
    public function createQuery(array $params, $query)
    {
        $search = isset($params['search']) ? $params['search']:'';

        if(!empty($search)){
            $q = 'title:'.$search.' OR organizationName:'.$search;
        }else{
            $q = '*:*';
        }

        $query->setQuery($q);
        
        if(isset($params['sort'])){
            $sorts = $this->filterSort($params['sort']);
            foreach($sorts as $field=>$order){
                $query->addSortField($field,$order);
            }
        }

        return $query;
    }

    static public function factory()
    {
        return new JobBoardPaginationQuery();
    }
}