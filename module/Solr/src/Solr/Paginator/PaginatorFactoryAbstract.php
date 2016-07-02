<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Paginator;

use Core\Paginator\PaginatorService;
use Solr\Paginator\Adapter\SolrAdapter;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract class for Solr paginator factory
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.27
 * @package Solr\Paginator
 */
abstract class PaginatorFactoryAbstract implements FactoryInterface,MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Set creation options
     *
     * @param  array $options
     *
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getCreationOptions()
    {
        return $this->options;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed|Paginator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var PaginatorService $serviceLocator */
        $filter             = $serviceLocator->getServiceLocator()->get('filterManager')->get($this->getFilter());
        $connectPath        = $this->getConnectPath();
        $solrClient         = $serviceLocator->getServiceLocator()->get('Solr/Manager')->getClient($connectPath);
        $resultConverter    = $serviceLocator->getServiceLocator()->get('Solr/ResultConverter');
        $adapter            = new SolrAdapter($solrClient,$filter,$resultConverter,$this->options);
        $service            = new Paginator($adapter);

        $this->setCreationOptions([]);
        return $service;
    }

    /**
     * pagination service name
     *
     * @return string
     */
    abstract protected function getFilter();

    /**
     * Get Solr Connect Path for this paginator
     *
     * @return string
     */
    abstract protected function getConnectPath();
}