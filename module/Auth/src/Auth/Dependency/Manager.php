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
use Zend\EventManager\EventInterface;
use Auth\Entity\UserInterface as User;
use Zend\Mvc\Router\RouteInterface as Router;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.27
 */
class Manager
{
    use EventManagerAwareTrait;
    
    const EVENT_GET_LISTS = 'getLists';
    const EVENT_REMOVE_ITEMS = 'removeItems';

    /**
     * @var DocumentManager
     */
    protected $documentManager;
    
    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }
    
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
        try {
            $this->getEventManager()->trigger(static::EVENT_REMOVE_ITEMS, $this, compact('user'));
            $this->documentManager->flush();
            return true;
        } catch (\Exception $e) {
            $this->documentManager->clear();
            return false;
        }
    }

    protected function attachDefaultListeners()
    {
        $this->getEventManager()->attach(static::EVENT_REMOVE_ITEMS, function (EventInterface $event) {
            $user = $event->getParam('user');
            foreach ($this->getLists() as $list) {
                foreach ($list->getEntities($user) as $entity) {
                    $this->documentManager->remove($entity);
                }
            }
        });
    }
}
