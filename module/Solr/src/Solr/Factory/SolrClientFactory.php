<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SolrClientFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var \Solr\Options\Connection $options */
        $options = $serviceLocator->get('Solr/Options/Connection');

        return new \SolrClient(array(
            'hostname' => $options->getHostname(),
            'port' => $options->getPort(),
            'path' => $options->getPath(),
            'login' => $options->getUsername(),
            'password' => $options->getPassword()
        ));
    }
}