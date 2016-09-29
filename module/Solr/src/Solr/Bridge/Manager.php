<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Bridge;

use Solr\Options\ModuleOptions;
use Zend\ServiceManager\ServiceLocatorInterface;
use SolrClient;

/**
 * Manage connection with the SolrServer
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since   0.26
 * @package Solr\Bridge
 */
class Manager
{
    const SOLR_DATE_FORMAT  = 'Y-m-d\TH:i:s\Z';

    /**
     * @var ModuleOptions
     */
    protected $options;
    
    /**
     * @var array
     */
    protected $clients = [];

    /**
     * Manager constructor.
     * @param ModuleOptions $options
     */
    public function __construct(ModuleOptions $options)
    {
        $this->options = $options;
    }

    /**
     * Get SolrClient with custom path option
     *
     * @param string $path
     * @return SolrClient
     */
    public function getClient($path = '/solr')
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
        $key = md5(implode(':', $options));
        
        if (!isset($this->clients[$key])) {
            $this->clients[$key] = new SolrClient($options);
        }

        return $this->clients[$key];
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
        return new static($sl->get('Solr/Options/Module'));
    }
}