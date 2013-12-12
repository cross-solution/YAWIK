<?php

namespace Core\Repository\Hydrator;

class KeywordsAwareEntityHydrator extends EntityHydrator
{
    
    protected $keywordsEnabledProperties = array();
    
    public function setKeywordsEnabledProperties(array $propertyList)
    {
        $this->keywordsEnabledProperties = $propertyList;
    }
    
    public function __construct(array $propertyList = array())
    {
        $this->setKeywordsEnabledProperties($propertyList);
        parent::__construct();
    }
    
    public function extract($object)
    {
        $data = parent::extract($object);
        
        $keywords = array();
        foreach ($this->keywordsEnabledProperties as $property) {
             $keywords = array_merge($keywords, $this->filterKeywords($object->$property));
        }
        $data['keywords'] = $keywords;

        return $data;
    }
    
    protected function filterKeywords($text)
    {
        $innerPattern = '[^a-z0-9ßäöü ]'; 
        $pattern      = '~' . $innerPattern . '~is';
        $stripPattern = '~^' . $innerPattern . '+|' . $innerPattern . '+$~is';
        $parts     = array();
        $textParts = explode(' ', $text);
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