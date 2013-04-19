<?php

namespace Core\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Core\Mapper\Query\Query;

class ListQuery extends AbstractPlugin
{
    
    protected $propertiesMap = array();
    protected $pageParamName = 'page';
    protected $itemsPerPage = 25;
    protected $queryKeysLowercased = true;
    protected $sortParamName = 'sort';
    
    public function __invoke($options=null)
    {
        if (null === $options) {
            return $this;
        }
        
        if (is_array($options)) {
            if (isset($options['properties_map'])) {
                $this->setPropertiesMap($options['properties_map']);
            }
        
            if (isset($options['page_param'])) {
                $this->setPageParamName($options['page_param']);
            }
            
            if (isset($options['items_per_page'])) {
                $this->setItemsPerPage($options['items_per_page']);
            }
        
            if (isset($options['query_keys_lowercased'])) {
                $this->setQueryKeysLowercased($options['query_key_lowercased']);
            }
            
            if (isset($options['sort_param'])) {
                $this->setSortParamName($options['sort_param']);
            }
        }
        
        return $this->getQuery();
    }
    
	/**
     * @return the $propertiesMap
     */
    public function getPropertiesMap ()
    {
        return $this->propertiesMap;
    }

	/**
     * @param multitype: $propertiesMap
     */
    public function setPropertiesMap ($propertiesMap)
    {
        $this->propertiesMap = $propertiesMap;
    }

	/**
     * @return the $pageParamName
     */
    public function getPageParamName ()
    {
        return $this->pageParamName;
    }

	/**
     * @param string $pageParamName
     */
    public function setPageParamName ($pageParamName)
    {
        $this->pageParamName = $pageParamName;
    }

    /**
     * @return the $sortParam
     */
    public function getSortParamName ()
    {
        return $this->sortParamName;
    }

	/**
     * @param field_type $sortParam
     */
    public function setSortParamName ($sortParam)
    {
        $this->sortParamName = $sortParam;
    }

	public function getQueryKeysLowercased()
    {
        return $this->queryKeysLowercased;
    }
    
    public function setQueryKeysLowercased($flag)
    {
        $this->queryKeysLowercased = (bool) $flag;
    }
    
	/**
     * @return the $itemsPerPage
     */
    public function getItemsPerPage ()
    {
        return $this->itemsPerPage;
    }

	/**
     * @param number $itemsPerPage
     */
    public function setItemsPerPage ($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getQuery()
    {
        $dbQuery = $this->getController()->getServiceLocator()->get('query');
        $criteria = $dbQuery->criteria();
        
        $request = $this->getController()->getRequest();
        $query = $request->getQuery()->toArray();
        
        foreach ($this->propertiesMap as $name => $criterion) {
            if (is_numeric($name)) {
                $name = $criterion;
                $criterion = "startsWith";
            }
            $queryName = $this->queryKeysLowercased ? strtolower($name) : $name;
            
            if (isset($query[$queryName])) {
                $criteria->$criterion($name, $query[$queryName]);
            }
        }
        $dbQuery->condition($criteria);
        $pageParamName = $this->queryKeysLowercased ? strtolower($this->getPageParamName()) : $this->getPageParamName();
        $page = $request->getQuery($pageParamName, 1);
        $itemsPerPage = $this->getItemsPerPage();
        $dbQuery->page($page, $itemsPerPage);
        
        $sortParamName = $this->queryKeysLowercased ? strtolower($this->getSortParamName()) : $this->getSortParamName();
        if (isset($query[$sortParamName])) {
            $sort = $query[$sortParamName];
        } else {
            reset($this->propertiesMap);
            list ($key, $val) = each($this->propertiesMap);
            $sort = is_numeric($key) ? $val : $key;
        }
        foreach (explode(',', $sort) as $s) {
            if ("-" == $s{0}) {
                $dbQuery->sort(substr($s, 1), false);
            } else {
                $dbQuery->sort($s);
            }
        }
        return $dbQuery;
    }
    
}
