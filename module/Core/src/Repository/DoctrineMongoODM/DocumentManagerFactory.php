<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** DocumentManagerFactory.php */
namespace Core\Repository\DoctrineMongoODM;

use Interop\Container\ContainerInterface;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Doctrine\ODM\MongoDB\DocumentManager as DoctrineDocumentManager;

/**
 * Class DocumentManagerFactory
 * @package Core\Repository\DoctrineMongoODM
 * @author  Anthonius Munthi <me@itstoni.com>
 */
class DocumentManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $container->setAllowOverride(true);
        $container->setFactory('doctrine.configuration.odm_default', new ConfigurationFactory('odm_default'));
        $container->setAllowOverride(false);
        
        $configFactory = new ConfigurationFactory('odm_default');
        $config = $configFactory->createService($container);
        
        $dm = $container->get('doctrine.documentmanager.odm_default');
        $dm = DoctrineDocumentManager::create($dm->getConnection(), $config, $dm->getEventManager());

        try {
            $dm->getSchemaManager()->ensureIndexes();
        } catch (ConnectionTimeoutException $e) {
            // provide a better way to handle this
            /* @var \Zend\ModuleManager\ModuleManager $moduleManager */
            $moduleManager = $container->get('ModuleManager');
            $modules = $moduleManager->getModules();
            if (in_array('Install', $modules)) {
                // we still in installation mode,
                // just ignore the error
                return $dm;
            } else {
                // not in installation mode, database should configured properly now
                // we should throw the error
                throw $e;
            }
        }

        return $dm;
    }
}
