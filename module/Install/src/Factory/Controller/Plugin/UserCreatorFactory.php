<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Factory\Controller\Plugin;

use Auth\Entity\Filter\CredentialFilter;
use Doctrine\ODM\MongoDB\Configuration;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Install\Controller\Plugin\UserCreator;
use Install\Filter\DbNameExtractor;
use Interop\Container\ContainerInterface;
use Zend\Filter\FilterPluginManager;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for an UserCreator plugin instance
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.20
 */
class UserCreatorFactory implements FactoryInterface
{
    /**
     * Create a UserCreator controller plugin
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return UserCreator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $filters = $container->get('FilterManager');

        $dbNameExtractor = $filters->get(DbNameExtractor::class);
        $credentialFilter = $filters->get(CredentialFilter::class);
        $database = $dbNameExtractor->filter($options['connection']);

        $config = $container->get('doctrine.documentmanager.odm_default')->getConfiguration();
        $config->setDefaultDB($database);
        $dm = $this->createDocumentManager($options['connection'],$config);

        $plugin = new UserCreator($credentialFilter,$dm);
        return $plugin;
    }

    /**
     * Create a document manager
     *
     * @param $connection
     * @param $config
     * @return DocumentManager
     * @codeCoverageIgnore
     */
    public function createDocumentManager($connection,$config)
    {
        $dbConn = new Connection($connection);
        $dm = DocumentManager::create($dbConn,$config);
        return $dm;
    }
}
