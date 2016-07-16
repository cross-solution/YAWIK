<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr;

use Core\ModuleManager\ModuleConfigLoader;
use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

/**
 * Class Module
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr
 */
class Module implements ConsoleUsageProviderInterface
{
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'solr index job' => 'Indexing active jobs',
        ];
    }

    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__.'/config');
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
}