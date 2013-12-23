<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ApplicationCollectionInterface.php */ 
namespace Applications\Entity;

use Core\Entity\CollectionInterface;
use Core\Entity\EntityInterface;

interface ApplicationCollectionInterface extends CollectionInterface
{
    public function countReadBy($userOrId);
    public function countUnreadBy($userOrId);
    
}

