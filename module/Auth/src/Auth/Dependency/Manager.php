<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Auth\Dependency;

use Zend\EventManager\EventManagerAwareTrait;
use Auth\Entity\UserInterface as User;
use Zend\Mvc\Router\RouteInterface as Router;

class Manager
{
    use EventManagerAwareTrait;
    
    const EVENT_GET_LISTS = 'getLists';
    const EVENT_REMOVE_ITEMS = 'removeItems';

    /**
     * @param User $user
     * @param Router $router
     * @return ListInterface[]
     */
    public function getLists()
    {
        return $this->getEventManager()->trigger(static::EVENT_GET_LISTS, $this);
    }
 
    /**
     * @param User $user
     * @param Router $router
     * @return \Zend\EventManager\ResponseCollection
     */
    public function removeItems(User $user)
    {
        return $this->getEventManager()->trigger(static::EVENT_REMOVE_ITEMS, $this, compact('user'));
    }
}
