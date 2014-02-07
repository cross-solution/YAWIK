<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** PropertyToKeywords.php */ 
namespace Core\Repository\Filter;

use Zend\Filter\FilterInterface;

class PropertyToKeywords implements FilterInterface
{
    
    public function filter($value)
    {
        $innerPattern = '[^a-z0-9ßäöü ]';
        $pattern      = '~' . $innerPattern . '~is';
        $stripPattern = '~^' . $innerPattern . '+|' . $innerPattern . '+$~is';
        $parts     = array();
        $textParts = explode(' ', $value);
        foreach ($textParts as $part) {
            $part = strtolower(trim($part));
            $part = preg_replace($stripPattern, '', $part);
        
            if ('' == $part) { continue; }
        
            $parts[] = $part;
        
            $tmpPart = $part;
            while (preg_match($pattern, $tmpPart, $match)) {
                $tmpPart = str_replace($match[0], ' ', $tmpPart);
            }
             
            if ($part != $tmpPart) {
                $tmpParts = explode(' ', $tmpPart);
                $tmpParts = array_filter($tmpParts);
                $parts = array_merge($parts, $tmpParts);
            }
        }
        return $parts;
        
    }
}

