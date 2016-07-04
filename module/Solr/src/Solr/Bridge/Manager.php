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
    protected $connectOption;

    /**
     * Manager constructor.
     * @param ModuleOptions $connectOption
     */
    public function __construct(ModuleOptions $connectOption)
    {
        $this->connectOption = $connectOption;
    }

    /**
     * Get \SolrClient with custom path option
     *
     * @param string $path
     * @return \SolrClient
     */
    public function getClient($path='/solr')
    {
        $connectOption = $this->connectOption;
        $options = [
            'secure' => $connectOption->isSecure(),
            'hostname' => $connectOption->getHostname(),
            'port' => $connectOption->getPort(),
            'path' => $path,
            'login' => $connectOption->getUsername(),
            'password' => $connectOption->getPassword(),
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
     * Create new instance for Solr\Manager
     * @param ServiceLocatorInterface $sl
     * @return Manager
     */
    static public function factory(ServiceLocatorInterface $sl)
    {
        return new self(
            $sl->get('Solr/Options/Module')
        );
    }
}