<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2018 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Listener;

use Core\Service\EntityEraser\AbstractDependenciesListener;
use Core\Service\EntityEraser\DependencyResult;
use Core\Service\EntityEraser\DependencyResultEvent;
use Jobs\Entity\Job;

/**
 * Listener checks the dependencies of a Job entity in the Jobs module itself.
 *
 * Which are only its snapshots at the moment.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class JobEntityDependencyListener extends AbstractDependenciesListener
{
    protected $entityClasses = [ Job::class ];

    protected function dependencyCheck(DependencyResultEvent $event)
    {
        /* @var \Jobs\Entity\Job $job */
        $job = $event->getEntity();
        $entities = $job->getSnapshots();

        if (count($entities)) {
            return [
                'Jobs/Snapshots',
                $entities,
                [
                    'description' => $event->isDelete() ? 'were removed.' : 'These snapshots will be removed:',
                    'mode' => DependencyResult::MODE_DELETE
                ]
            ];
        }
    }
}
