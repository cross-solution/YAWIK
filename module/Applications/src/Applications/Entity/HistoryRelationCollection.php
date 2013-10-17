<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** HistoryCollection.php */ 
namespace Applications\Entity;

use Core\Entity\RelationCollection;
use Core\Repository\EntityBuilder\EntityBuilderInterface;

class HistoryRelationCollection extends RelationCollection implements HistoryCollectionInterface
{
 
    /* Method stubs to satisfy HistoryCollectionInterface.
     * These methods are not needed in this class, but we implement
     * HistoryCollectionInterface for the sake of the type.
     */
    public function setEntityBuilder(EntityBuilderInterface $builder) {}
    public function getEntityBuilder() {}
    
    
    public function addFromStatus($status, $message='[System]') {
        $this->loadCollection();
        return $this->collection->addFromStatus($status, $message);
    }

}

