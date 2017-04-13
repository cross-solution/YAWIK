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

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class DeleteImageSetListener 
{

    private $config = [];

    public function __construct($repositories, array $config = [])
    {
        $this->repositories = $repositories;
        $this->config = $config;
    }

    public function __invoke(FileEvent $event)
    {
        $file = $event->getFile();
        $fileClass = get_class($file);

        if (!$file instanceOf ImageInterface || !isset($this->config[$fileClass])) {
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

        $imageSet = $entity->$getter();
        $imageSet->remove($this->repositories);

        $callback = [$entity, "remove$property"];
        if (is_callable($callback)) {
            call_user_func($callback);
        }

        return true;
    }
}