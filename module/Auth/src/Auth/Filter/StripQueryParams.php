<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** StripQueryParams.php */ 
namespace Auth\Filter;

use Zend\Filter\FilterInterface;

class StripQueryParams implements FilterInterface
{
    protected $stripParams = array(
        'logout',
    );
    
    
    public function setStripParams(array $params)
    {
        $this->stripParams = $params;
        return $this;
    }
    
    public function getStripParams()
    {
        return $this->stripParams;
    }
    
    public function filter($value, array $stripParams = array())
    {
        list($uri, $query) = explode('?', $value, 2);
        if (!$query) { return $uri; }
        
        if (empty($stripParams)) {
            $stripParams = $this->getStripParams();
        }
        
        parse_str($query, $queryParams);
        $queryParams = array_diff_key($queryParams, array_flip($stripParams));
        return $uri . (empty($queryParams) ? '' : '?' . http_build_query($queryParams));
            
    }
}

