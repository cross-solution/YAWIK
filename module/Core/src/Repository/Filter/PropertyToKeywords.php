<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PropertyToKeywords.php */
namespace Core\Repository\Filter;

use Zend\Filter\FilterInterface;
use Core\Entity\SearchableEntityInterface;
use Zend\Stdlib\StringUtils;

class PropertyToKeywords implements FilterInterface
{
    public function filter($value)
    {
        if ($value instanceof SearchableEntityInterface) {
            $entity = $value;
            $value = array();
            foreach ($entity->getSearchableProperties() as $name) {
                $result = $entity->{'get' .$name}();
                if (is_array($result)) {
                    $value = array_merge($value, $result);
                } else {
                    $value[] = $result;
                }
            }
        } elseif (!is_array($value)) {
            $value = array($value);
        }
        
        $keywords = array();
        
        foreach ($value as $val) {
            $keywords = array_merge($keywords, $this->getKeywords($val));
        }
        $keywords = array_unique($keywords);
        return $keywords;
    }
    
    protected function getKeywords($string)
    {
        $innerPattern = StringUtils::hasPcreUnicodeSupport()
                      ? '[^\p{L}]'
                      : '[^a-z0-9ßäöü ]';
        $pattern      = '~' . $innerPattern . '~isu';
        $stripPattern = '~^' . $innerPattern . '+|' . $innerPattern . '+$~isu';
        $parts     = array();
        $textParts = explode(' ', $string);
        foreach ($textParts as $part) {
            $part = strtolower(trim($part));
            $part = preg_replace($stripPattern, '', $part);
        
            if ('' == $part) {
                continue;
            }
        
            $parts[] = $part;
        
            $tmpPart = preg_replace($pattern, ' ', $part);
             
            if ($part != $tmpPart) {
                $tmpParts = explode(' ', $tmpPart);
                $tmpParts = array_filter($tmpParts);
                $parts = array_merge($parts, $tmpParts);
            }
        }
        return $parts;
    }
}
