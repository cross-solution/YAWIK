<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Listener;

use Core\Service\EntityEraser\DependencyResultEvent;
use Jobs\Entity\Job;
use MongoDB\BSON\ObjectId;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class LoadDependendEntities
{
    public function __invoke(DependencyResultEvent $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Job) {
            $entities = $event->getRepository('Applications')->findBy(['isDraft' => null, 'job' => new ObjectId($entity->getId())]);

            return ['Applications', $entities, 'These applications references the job and will also be removed:' ];
        }
    }

    public function onDelete(DependencyResultEvent $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Job) {
            $repository = $event->getRepository('Applications');
            $entities = $repository->findBy(['isDraft' => null, 'job' => new ObjectId($entity->getId())]);
            foreach ($entities as $ent) {
                $repository->remove($ent);
            }
            return ['Applications', $entities, 'were removed.' ];
        }
    }
}
