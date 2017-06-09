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
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

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
		
		$dm = $container->get('doctrine.documentmanager.odm_default');
		
		if (\Zend\Console\Console::isConsole()) {
			$configFactory = new ConfigurationFactory('odm_default');
			$config = $configFactory->createService($container);
			$dm = \Doctrine\ODM\MongoDB\DocumentManager::create($dm->getConnection(), $config, $dm->getEventManager());
		}
		
		$dm->getSchemaManager()->ensureIndexes();
		return $dm;
	}
	
	/**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
		return $this($serviceLocator,DocumentManagerFactory::class);
    }
}
