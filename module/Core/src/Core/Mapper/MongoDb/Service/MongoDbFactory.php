<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Services of MongoDb mappers */
namespace Core\Mapper\MongoDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * MongoDb Factory 
 */
class MongoDbFactory implements FactoryInterface
{

    /**
     * Creates a instance of a MongoDb-Handler.
     * Connects to the mongo server via the connection string
     * given in the application config array under the key
     * ["database"]["connection"].
     * It then switches to the database under the key
     * ["database"]["databaseName"]
     * 
     * @param \Zend\ServiceManager\ServiceLocatorinterface $serviceLocator
     * @return \MongoDB
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $options = $serviceLocator->get('Config');
        $conn = new \MongoClient($options['database']['connection']);
        $db = $conn->{$options['database']['databaseName']};
        return $db;
    }
}


    