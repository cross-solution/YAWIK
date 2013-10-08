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
        $value_array = explode('?', $value, 2);
        $uri = $value_array[0];
        $query = '';
        if (count($value_array) < 2 || empty($value_array[1])) {
            return $uri;
        }
        
        if (empty($stripParams)) {
            $stripParams = $this->getStripParams();
        }
        
        parse_str($query, $queryParams);
        $queryParams = array_diff_key($queryParams, array_flip($stripParams));
        return $uri . (empty($queryParams) ? '' : '?' . http_build_query($queryParams));
            
    }
}

