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

/**
 * Provide adapter for Solr type paginator
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Paginator\Adapter
 */
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
     * @param   array                       $params
     */
    public function __construct($client,$filter,$resultConverter,$params=array())
    {
        $this->client           = $client;
        $this->filter           = $filter;
        $this->resultConverter  = $resultConverter;
        $this->params           = $params;
    }
    
    /**
     * @inheritdoc
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->resultConverter->convert(
            $this->filter,
            $this->getResponse($offset,$itemCountPerPage)
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
     * Process query into server
     *
     * @param   int     $offset
     * @param   int     $itemCountPerPage
     * @return  \SolrQueryResponse
     * @throws  \Exception
     */
    protected function getResponse($offset=0,$itemCountPerPage=0)
    {
        $id = md5($offset.$itemCountPerPage);
        if(!isset($this->responses[$id])){
            $query = new \SolrQuery();
            $query = $this->filter->filter($this->params,$query);
            $query->setStart($offset);
            $query->setRows($itemCountPerPage);
            try{
                $this->responses[$id] = $this->client->query($query);
            }catch (\Exception $e){
                $message = 'Failed to process query';
                throw new ServerException($message,$e->getCode(),$e);
            }
        }

        return $this->responses[$id];
    }
}