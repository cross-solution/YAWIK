<?php

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * @todo Write factory, configuration must be possible
 * @author mathias
 *
 */
class BuildQuery extends AbstractHelper
{
    protected $queryParams;
    
    public function __invoke(array $params=array(), $reuseQueryParameters=false)
    {
        return $this->buildQuery($params, $reuseQueryParameters);
    }

    
    public function getQueryParams()
    {
        if (!$this->queryParams) {
            $this->queryParams = $this->getView()
                                ->getHelperPluginManager()
                                ->getServiceLocator()
                                ->get('Application')
                                ->getRequest()
                                ->getQuery()
                                ->toArray();
        }
        return $this->queryParams;
    }
    
    public function buildQuery(array $params=array(), $reuseQueryParameters=false)
    {
        if ($reuseQueryParameters) {
            $params = array_merge($this->getQueryParams(), $params);
        }
        return empty($params) ? '' : '?' . \Zend\Uri\Uri::encodeQueryFragment(http_build_query($params));
    }
    
    

    
}