<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** DocumentManagerFactory.php */ 
namespace Core\Repository\DoctrineMongoODM;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class DocumentManagerFactory implements FactoryInterface 
{
    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator->setAllowOverride(true);
        $serviceLocator->setFactory('doctrine.configuration.odm_default', new ConfigurationFactory('odm_default'));
        $serviceLocator->setAllowOverride(false);
        $dm = $serviceLocator->get('doctrine.documentmanager.odm_default');
#        $dm->getSchemaManager()->ensureIndexes();
        return $dm;
    }
}
