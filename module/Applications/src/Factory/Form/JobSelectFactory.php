<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Applications\Factory\Form;

use Applications\Form\Element\JobSelect;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for a job select element
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29.2
 */
class JobSelectFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var \Laminas\Http\PhpEnvironment\Request $request */
        $request = $container->get('Request');
        $query   = $request->getQuery();
        $jobId   = $query->get('job');
        $select = new JobSelect();

        if ($jobId) {
            /* @var \Jobs\Repository\Job $repository */
            $repositories = $container->get('repositories');
            $repository   = $repositories->get('Jobs');
            $job          = $repository->find($jobId);
            $select->setSelectedJob($job);
        }

        return $select;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var \Laminas\ServiceManager\AbstractPluginManager $serviceLocator */
        return $this($serviceLocator, JobSelect::class);
    }
}
