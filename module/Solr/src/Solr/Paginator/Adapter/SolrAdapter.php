<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Paginator\Adapter;

use Solr\Filter\AbstractPaginationQuery;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Stdlib\Parameters;

class SolrAdapter implements AdapterInterface
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
     * @var \SolrQuery
     */
    protected $query;

    /**
     * Store current query response from solr server
     *
     * @var \SolrQueryResponse
     */
    protected $response;

    /**
     * @var AbstractPaginationQuery
     */
    protected $filter;

    /**
     * SolrAdapter constructor.
     *
     * @param \SolrClient             $client
     * @param AbstractPaginationQuery $filter
     * @param array                   $params
     */
    public function __construct($client,$filter,$params=array())
    {
        $this->client = $client;
        $this->params = $params;
        $this->filter = $filter;
        $this->query = new \SolrQuery();
    }


    public function getItems($offset, $itemCountPerPage)
    {
        $results = $this->getResponse()->getArrayResponse();
        $docs = $results['response']['docs'];
        return $docs;
    }

    public function count()
    {
        $response = $this->getResponse()->getArrayResponse();
        return $response['response']['numFound'];
    }

    /**
     * @param   int     $offset
     * @param   int     $itemCountPerPage
     * @return \SolrQueryResponse
     */
    protected function getResponse($offset=0,$itemCountPerPage=5)
    {
        if(!is_object($this->response)){
            $query = new $this->query;
            $query = $this->filter->filter($this->params,$query);
            $query->setStart($offset);
            $query->setRows($itemCountPerPage);
            $this->response = $this->client->query($query);
        }
        return $this->response;
    }
}