<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Applications\Listener;

use Core\Service\EntityEraser\DependencyResultEvent;
use Jobs\Entity\Job;

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
            $entities = $event->getRepository('Applications')->findBy(['isDraft' => null, 'job' => new \MongoId($entity->getId())]);

            return ['Applications', $entities, 'These applications references the job and will also be removed:' ];
        }
    }

    public function onDelete(DependencyResultEvent $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Job) {
            $repository = $event->getRepository('Applications');
            $entities = $repository->findBy(['isDraft' => null, 'job' => new \MongoId($entity->getId())]);
            foreach ($entities as $ent) {
                $repository->remove($ent);
            }
            return ['Applications', $entities, 'were removed.' ];
        }
    }
}
