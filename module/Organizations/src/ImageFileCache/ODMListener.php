<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Organizations\ImageFileCache;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Organizations\Entity\Organization;
use Organizations\Entity\OrganizationImage;

/**
 * Image file cache ODM listener
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ODMListener implements EventSubscriber
{
    
    /**
     * @var Manager
     */
    protected $manager;
    
    /**
     * @var array
     */
    protected $delete = [];
    
    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
     */
    public function getSubscribedEvents()
    {
        if (! $this->manager->isEnabled()) {
            return [];
        }
        
        return [
            Events::preUpdate,
            Events::postFlush
        ];
    }
    
    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $organization = $eventArgs->getDocument();
        
        // check for a organization instance
        if (! $organization instanceof Organization) {
            return;
        }
        
        // check if the image has been changed
        if (! $eventArgs->hasChangedField('image')) {
            return;
        }
        
        $image = $eventArgs->getOldValue('image');
        
        // check if any image existed
        if (! $image instanceof OrganizationImage) {
            return;
        }
        
        // mark image for deletion
        $this->delete[] = $image;
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        foreach ($this->delete as $image) {
            $this->manager->delete($image);
        }
    }
}
