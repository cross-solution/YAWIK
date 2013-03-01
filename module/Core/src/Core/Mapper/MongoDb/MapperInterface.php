<?php
/**
 * Cross Applicant Management
 *
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Core\Mapper\MongoDb;

use Core\Mapper\MapperInterface as CoreMapperInterface;

/**
 *
 */
interface MapperInterface extends CoreMapperInterface
{

    public function setCollection(\MongoCollection $collection);
    public function getCollection();
}