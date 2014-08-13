<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
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
     * The MongoClient instance
     * @var \MongoClient
     */
    protected $_mongoClient;
    
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
//        var_dump($options['database']['connection']); exit;
        $conn = $this->getMongoClient($options['database']['connection']);
        $db = $conn->{$options['database']['databaseName']};
        return $db;
    }
    
    /**
     * Sets the MongoClient instance.
     * 
     * @param \MongoClient $client
     * @return MongoDbFactory
     */
    public function setMongoClient(\MongoClient $client)
    {
        $this->_mongoClient = $client;
        return $this;
    }
    
    /**
     * Gets the MongoClient instance.
     * 
     * If no instance is set, it will create one using the $connectionString
     * as argument to __construct
     * 
     * @param string $connectionString
     * @return \MongoClient
     */
    public function getMongoClient($connectionString = "")
    {
        // @codeCoverageIgnoreStart
        if (!$this->_mongoClient) {
            $this->_mongoClient = new \MongoClient($connectionString);
        }
        // @codeCoverageIgnoreEnd
        return $this->_mongoClient;
    }
}


    