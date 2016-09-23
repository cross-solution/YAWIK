<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Paginator\Adapter;

use Solr\Exception\ServerException;
use Solr\Filter\AbstractPaginationQuery;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Stdlib\Parameters;
use Solr\Bridge\ResultConverter;
use Solr\FacetsProviderInterface;
use Solr\Facets;

/**
 * Provide adapter for Solr type paginator
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 * @package Solr\Paginator\Adapter
 */
class SolrAdapter implements AdapterInterface, FacetsProviderInterface
{
    /**
     * @var \SolrClient
     */
    protected $client;

    /**
     * @var Parameters
     */
    protected $params;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var Facets
     */
    protected $facets;

    /**
     * Store current query response from solr server
     * based on offset and count per page
     *
     * @var \SolrQueryResponse[]
     */
    protected $responses;

    /**
     * @var AbstractPaginationQuery
     */
    protected $filter;

    /**
     * @var ResultConverter
     */
    protected $resultConverter;

    /**
     * SolrAdapter constructor.
     *
     * @param   \SolrClient                 $client
     * @param   AbstractPaginationQuery     $filter
     * @param   ResultConverter             $resultConverter
     * @param   Facets                      $facets
     * @param   array                       $params
     */
    public function __construct($client, $filter, $resultConverter, Facets $facets, $params = array())
    {
        $this->client = $client;
        $this->filter = $filter;
        $this->resultConverter = $resultConverter;
        $this->facets = $facets;
        $this->params = $params;
    }
    
    /**
     * @inheritdoc
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->resultConverter->convert(
            $this->filter,
            $this->getResponse($offset,$itemCountPerPage)->getResponse()
        );
    }

    /**
     * @inheritdoc
     * @return  mixed
     * @throws \Exception
     */
    public function count()
    {
        $response = $this->getResponse()->getArrayResponse();
        return $response['response']['numFound'];
    }
    
    /**
	 * @see \Solr\FacetsProviderInterface::getFacets()
	 */
	public function getFacets()
	{
		return $this->facets->setFacetResult($this->getResponse()->getResponse()->facet_counts);
	}

    /**
     * Process query into server
     *
     * @param   int     $offset
     * @param   int     $itemCountPerPage
     * @return  \SolrQueryResponse
     * @throws  ServerException
     */
    protected function getResponse($offset=0,$itemCountPerPage=0)
    {
        $id = md5($offset.$itemCountPerPage);
        if (!isset($this->responses[$id])) {
            $query = new \SolrDisMaxQuery();
            $this->filter->filter($this->params, $query, $this->facets);
            $query->setStart($offset);
            $query->setRows($itemCountPerPage);
            try {
                $this->responses[$id] = $this->client->query($query);
            } catch (\Exception $e) {
                throw new ServerException('Failed to process query', $e->getCode(), $e);
            }
        }

        return $this->responses[$id];
    }
}