<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
        if (false === strpos($value, '?')) {
            return $value;
        }
        
        list($uri, $query) = explode('?', $value, 2);
        
        if (empty($stripParams)) {
            $stripParams = $this->getStripParams();
        }
        
        parse_str($query, $queryParams);
        $queryParams = array_diff_key($queryParams, array_flip($stripParams));
        return $uri . (empty($queryParams) ? '' : '?' . http_build_query($queryParams));
            
    }
}

