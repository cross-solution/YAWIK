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
use Solr\Bridge\ResultConverter;
use Solr\Options\ModuleOptions;
use Solr\Paginator\Adapter\SolrAdapter;
use Zend\Paginator\Paginator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Abstract class for Solr paginator factory
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
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
     * @return Paginator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var PaginatorService $serviceLocator */
        /* @var ResultConverter $resultConverter */
        $serviceManager     = $serviceLocator->getServiceLocator();
        $filter             = $serviceManager->get('filterManager')->get($this->getFilter());
        $options            = $serviceManager->get('Solr/Options/Module');
        $connectPath        = $this->getConnectPath($options);
        $solrClient         = $serviceManager->get('Solr/Manager')->getClient($connectPath);
        $resultConverter    = $serviceManager->get('Solr/ResultConverter');
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
     *
     * Get connection path for this paginator
     *
     * @param   ModuleOptions $options
     * @return  string
     */
    abstract protected function getConnectPath(ModuleOptions $options);
}