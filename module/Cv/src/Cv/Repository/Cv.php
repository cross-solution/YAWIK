<?php

namespace Cv\Repository;

use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Repository\AbstractRepository;
use Core\Entity\EntityInterface;


class Cv extends AbstractRepository
{
    
    public function getPaginatorCursor($params)
    {
        $criteria = array();
        $by = $params->get('by', 'me');
        if ('me' == $by) {
            $user = $this->getService('AuthenticationService')->getUser();
            $criteria['user'] = $user->id;
        }

        $sort = $params->get('sortField', 'date');
        switch ($sort) {
            case "date":
            default:
                $sort = 'dateCreated';
                break;
        }
        
        
    	$cursor = $this->findBy($criteria);
    	$cursor->sort(array($sort => $params->get('sortDir', 1)));
    	return $cursor;
    }
    
}