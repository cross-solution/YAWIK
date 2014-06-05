<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Core MongoDb mappers */
namespace Core\Mapper\MongoDb;

use Core\Mapper\MapperInterface as CoreMapperInterface;

/**
 * MongoDb mapper interface
 */
interface MapperInterface extends CoreMapperInterface
{

    /**
     * Sets the mongodb collection object.
     * 
     * @param \MongoCollection $collection
     */
    public function setCollection(\MongoCollection $collection);
    
    /**
     * Gets the mongodb collection object.
     * 
     * @return \MongoCollection
     */
    public function getCollection();
}