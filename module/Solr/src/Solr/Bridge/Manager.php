<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Bridge;


use Solr\Exception\ServerException;
use Solr\Options\ModuleOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Manage connection with the SolrServer
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Bridge
 */
class Manager
{
    const SOLR_DATE_FORMAT  = 'Y-m-d\TH:i:s\Z';
    const SORT_ASCENDING    = 0;
    const SORT_DESCENDING   = 1;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * Manager constructor.
     * @param ModuleOptions $options
     */
    public function __construct(ModuleOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Get \SolrClient with custom path option
     *
     * @param string $path
     * @return \SolrClient
     */
    public function getClient($path='/solr')
    {
        $options = $this->options;
        $options = [
            'secure' => $options->isSecure(),
            'hostname' => $options->getHostname(),
            'port' => $options->getPort(),
            'path' => $path,
            'login' => $options->getUsername(),
            'password' => $options->getPassword(),
            'wt' => 'phps'
        ];

        return new \SolrClient($options);
    }

    /**
     * Add new document into Solr server
     *
     * @param \SolrInputDocument $document
     * @param string $path
     * @throws ServerException When failed adding document to server
     */
    public function addDocument(\SolrInputDocument $document,$path='/solr')
    {
        $client = $this->getClient($path);
        try{
            $client->addDocument($document);
            $client->commit();
            $client->optimize();
        }catch (\Exception $e){
            throw new ServerException('Can not add document to server!',$e->getCode(),$e);
        }
    }

    /**
     * @return ModuleOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Create new instance for Solr\Manager
     * @param ServiceLocatorInterface $sl
     * @return Manager
     */
    static public function factory(ServiceLocatorInterface $sl)
    {
        /* @var ModuleOptions $options */
        $options = $sl->get('Solr/Options/Module');
        return new self($options);
    }
}