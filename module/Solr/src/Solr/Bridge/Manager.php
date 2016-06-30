<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Bridge;


use Solr\Options\Connection as ConnectionOption;
use Zend\ServiceManager\ServiceLocatorInterface;

class Manager
{
    const SOLR_DATE_FORMAT  = 'Y-m-d\TH:i:s\Z';
    const SORT_ASCENDING    = 0;
    const SORT_DESCENDING   = 1;

    /**
     * @var ConnectionOption
     */
    protected $connectOption;

    public function __construct(ConnectionOption $connectOption)
    {
        $this->connectOption = $connectOption;
    }

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

    static public function factory(ServiceLocatorInterface $sl)
    {
        return new self(
            $sl->get('Solr/Options/Connection')
        );
    }
}