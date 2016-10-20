<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Organizations\ImageFileCache;

use Zend\EventManager\EventInterface;
use Organizations\Entity\OrganizationImage;

/**
 * Image file cache image entity listener
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ImageListener
{

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param EventInterface $event
     */
    public function __invoke(EventInterface $event)
    {
        $image = $event->getTarget();
        
        if (! $image instanceof OrganizationImage) {
            return;
        }
        
        if (! $this->manager->isEnabled()) {
            return;
        }
        
        return $this->manager->getUri($image);
    }
}
