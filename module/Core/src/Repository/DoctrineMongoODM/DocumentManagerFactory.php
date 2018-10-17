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
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null ) {
		$container->setAllowOverride(true);
		$container->setFactory('doctrine.configuration.odm_default', new ConfigurationFactory('odm_default'));
		$container->setAllowOverride(false);
		
		$configFactory = new ConfigurationFactory('odm_default');
		$config = $configFactory->createService($container);
		
		$dm = $container->get('doctrine.documentmanager.odm_default');
		$dm = DoctrineDocumentManager::create($dm->getConnection(), $config, $dm->getEventManager());
		$dm->getSchemaManager()->ensureIndexes();
		return $dm;
	}
}
