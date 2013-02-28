<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Mapper\Mongo;

use Core\Mapper\MapperInterface as CoreMapperInterface;

/**
 *
 */
interface MapperInterface extends CoreMapperInterface
{

    public function setDatabase(\MongoDb $database);
    public function getDatabase();
    public function setCollection($collection);
    public function getCollection();
}