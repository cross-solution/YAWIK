<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */

namespace Solr;

use IteratorAggregate;
use Countable;
use ArrayIterator;
use ArrayAccess;
use InvalidArgumentException;
use SolrDisMaxQuery;
use SolrUtils;

class Facets implements IteratorAggregate, Countable
{
    
    /**
     * @var ArrayAccess
     */
    protected $facetResult;
    
    /**
     * @var array
     */
    protected $params;
    
    /**
     * @var array
     */
    protected $definitions;
    
    /**
     * @var array
     */
    protected $queryMethodMap = [
        'facet_fields' => 'addFacetField'
    ];
    
    /**
     * @var array
     */
    protected $cache;

    public function __construct()
    {
        $this->params = [];
        $this->definitions = [
            'regionList' => [
                'type' => 'facet_fields',
                'title' => /*@translate*/ 'Regions'
            ]
        ];
    }

    /**
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->toArray());
		
	}

    /**
	 * @see Countable::count()
	 */
	public function count()
	{
		return count($this->toArray());
	}

    /**
     * @return array
     */
    public function toArray()
    {
        if (!isset($this->cache)) {
            $this->cache = [];
            
            foreach ($this->definitions as $name => $definition) {
                // check if the facet definition exists in the facet result
                if (!isset($this->facetResult[$definition['type']])
                    || !isset($this->facetResult[$definition['type']][$name])
                ) {
                    continue;
                }
                
                // cast to array to workaround the 'Notice: Illegal member variable name' for PHP <= 5.6.20
                $result = (array)$this->facetResult[$definition['type']][$name];
                // remove empty value
                unset($result['']);
                
                $this->cache[$name] = $result;
            }
        }
        
        return $this->cache;
    }
    
    /**
     * @param ArrayAccess $facetResult
     * @return Facets
     */
    public function setFacetResult(ArrayAccess $facetResult)
    {
        $this->facetResult = $facetResult;
        $this->cache = null;
        
        return $this;
    }
    /**
	 * @param array $params
	 * @return Facets
	 */
	public function setParams(array $params)
	{
		$this->params = $params;
		
		return $this;
	}

    /**
     * @param SolrDisMaxQuery $query
     * @return Facets
     */
    public function setupQuery(SolrDisMaxQuery $query)
    {
        $query->setFacet(true);
        
        foreach ($this->definitions as $name => $definition) {
            $tag = sprintf('tag-%s', $name);
            $method = $this->queryMethodMap[$definition['type']];
            $query->$method(sprintf('{!ex=%s}%s', $tag, $name));
            
            if (isset($this->params[$name]) && is_array($this->params[$name])) {
                $valueList = array_filter(array_map(function ($value) {
                    return trim(SolrUtils::escapeQueryChars($value));
                }, array_keys($this->params[$name])));
                
                if ($valueList) {
                    $query->addFilterQuery(sprintf('{!tag=%s}%s:(%s)', $tag, $name, implode(' OR ', $valueList)));
                }
            }
        }
        
        return $this;
    }
    
    /**
     * @param string $name
     * @param string $value
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function isValueActive($name, $value)
    {
        $this->assertValidName($name);
        
        return isset($this->params[$name])
            && is_array($this->params[$name])
            && isset($this->params[$name][$value]);
    }
    
    /**
     * @return array
     */
    public function getActiveValues()
    {
        $return = [];
        
        foreach ($this->toArray() as $name => $values) {
            if (isset($this->params[$name])
                && is_array($this->params[$name])
                && $this->params[$name]
            ) {
                $activeValues = [];
                
                foreach ($values as $value => $count) {
                    if (isset($this->params[$name][$value])) {
                        $activeValues[] = $value;
                    }
                }
                
                if ($activeValues) {
                    $return[$name] = $activeValues;
                }
            }
        }
        
        return $return;
    }
    
    /**
     * @param string $name
     * @return string Non-translated title
     * @throws InvalidArgumentException
     */
    public function getTitle($name)
    {
        $this->assertValidName($name);
        
        return $this->definitions[$name]['title'];
    }
    
    /**
     * @param string $name
     * @throws InvalidArgumentException
     */
    protected function assertValidName($name)
    {
        if (! isset($this->definitions[$name])) {
            throw new InvalidArgumentException();
        }
    }
}