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

class ApplicationRelationCollection extends RelationCollection implements ApplicationCollectionInterface
{
 
    public function countReadBy($userOrId)
    {
        $this->loadCollection();
        return $this->collection->countReadBy($userOrId);
    }
    
    public function countUnreadBy($userOrId)
    {
        $this->loadCollection();
        return $this->collection->countUnreadBy($userOrId);
    }
}

