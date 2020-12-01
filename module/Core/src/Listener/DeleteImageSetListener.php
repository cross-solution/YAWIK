<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Core\Listener;

use Core\Entity\ImageInterface;
use Core\Entity\ImageMetadata;
use Core\Entity\ImageSetInterface;
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

        /* @var ImageMetadata $metadata */
        $metadata = $file->getMetadata();
        $config = $this->config[$fileClass];

        $repository = $this->repositories->get($config['repository']);
        $property   = $config['property'];
        $getter     = "get$property";
        $dbKey      = "$property.id";
        $entity     = $repository->findOneBy([$dbKey => $metadata->getBelongsTo()]);

        if (!$entity) {
            return false;
        }

        /* @var ImageSetInterface $imageSet */
        $imageSet = $entity->$getter();
        $imageSet->clear();
        $repository->store($entity);
        return true;
    }
}
