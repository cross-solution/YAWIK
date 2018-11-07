<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Listener;

use Core\Entity\ImageInterface;
use Core\Listener\Events\FileEvent;
use Core\Repository\RepositoryService;

/**
 * Deletes/Clear image sets if one image of this set is deleted.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class DeleteImageSetListener
{

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var RepositoryService
     */
    private $repositories;

    /**
     * @param RepositoryService $repositories
     * @param array             $config
     */
    public function __construct(RepositoryService $repositories, array $config = [])
    {
        $this->repositories = $repositories;
        $this->config = $config;
    }

    /**
     * @param FileEvent $event
     *
     * @return bool
     */
    public function __invoke(FileEvent $event)
    {
        $file = $event->getFile();
        $fileClass = get_class($file);

        if (!$file instanceof ImageInterface || !isset($this->config[$fileClass])) {
            return false;
        }

        $config = $this->config[$fileClass];

        $repository = $this->repositories->get($config['repository']);
        $property   = $config['property'];
        $getter     = "get$property";
        $dbKey      = "$property.id";
        $entity     = $repository->findOneBy([$dbKey => $file->belongsTo()]);

        if (!$entity) {
            return false;
        }

        /* @var \Core\Entity\ImageSetInterface $imageSet */
        $imageSet = $entity->$getter();
        $imageSet->clear();

        return true;
    }
}
