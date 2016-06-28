<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr;


use Solr\Event\JobEventSubscriber;
use Solr\Factory\ConnectionOptionFactory;
use Solr\Factory\SolrClientFactory;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module implements ServiceProviderInterface
{
    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Loads module specific autoloader configuration.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {

    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Solr/Client' => new SolrClientFactory(),
                'Solr/Options/Connection' => new ConnectionOptionFactory(),
                'Solr/Event/JobEventSubscriber' => 'Solr\Event\JobEventSubscriber::factory'
            ],
        ];
    }


}