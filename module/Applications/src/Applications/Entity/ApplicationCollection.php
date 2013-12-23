<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** ApplicationCollection.php */ 
namespace Applications\Entity;

use Core\Entity\Collection;
use Core\Entity\EntityInterface;

class ApplicationCollection extends Collection implements ApplicationCollectionInterface
{
    public function countReadBy($userOrId)
    {
        return $this->getCount($userOrId);
    }
    
    public function countUnreadBy($userOrId)
    {
        return $this->getCount($userOrId, true);
    }
    
    protected function getCount($userOrId, $unread=false)
    {
        $method = 'is' . ($unread ? 'Unread' : 'Read') . 'By';
        $count = 0;
        foreach ($this as $app) {
            if ($app->$method($userOrId)) {
                $count += 1;
            }
        }
        return $count;
    }
    
}
