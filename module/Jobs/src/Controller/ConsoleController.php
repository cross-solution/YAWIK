<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ConsoleController of Jobs */
namespace Jobs\Controller;

use Core\Queue\LazyJob;
use Core\Repository\RepositoryService;
use Interop\Container\ContainerInterface;
use Jobs\Entity\StatusInterface;
use Jobs\Queue\FindJobsWithExternalImageJob;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ProgressBar\ProgressBar;
use Core\Console\ProgressBar as CoreProgressBar;
use Zend\ProgressBar\Adapter\Console as ConsoleAdapter;
use Auth\Entity\UserInterface;

class ConsoleController extends AbstractActionController
{
    /**
     * @var RepositoryService
     */
    private $repositories;
    
    public function __construct(
        RepositoryService $repositories
    ) {
        $this->repositories = $repositories;
    }
    
    public static function factory(ContainerInterface $container)
    {
        return new self(
            $container->get('repositories')
        );
    }
    
    public function expireJobsAction()
    {
        $repositories = $this->repositories;
        /* @var \Jobs\Repository\Job $jobsRepo */
        $jobsRepo     = $repositories->get('Jobs/Job');
        $days = (int) $this->params('days');
        $limit = (string) $this->params('limit');
        $info = $this->params('info');

        if (!$days) {
            return 'Invalid value for --days. Must be integer.';
        }

        $date = new \DateTime('today');
        $date->sub(new \DateInterval('P' . $days . 'D'));

        $query        = [
            '$and' => [
                ['status.name' => StatusInterface::ACTIVE],
                ['$or' => [
                    ['datePublishStart.date' => ['$lt' => $date]],
                    ['datePublishEnd.date' => ['$lt' => new \DateTime('today midnight')]],
                ]],
                ['$or' => [
                    ['isDeleted' => ['$exists' => false]],
                    ['isDeleted' => false],
                ]]
            ]
        ];

        $offset = 0;
        if ($limit && false !== strpos($limit, ',')) {
            list($limit, $offset) = explode(',', $limit);
        }

        $jobs = $jobsRepo->findBy($query, null, (int) $limit, (int) $offset);
        $count = count($jobs);

        if (0 === $count) {
            return 'No jobs found.';
        }

        if ($info) {
            echo count($jobs) , ' Jobs';
            if ($offset) {
                echo ' starting from ' . $offset;
            }
            echo PHP_EOL . PHP_EOL;
            $this->listExpiredJobs($jobs);
            return;
        }
        
//        foreach ($repositories->getEventManager()->getListeners('preUpdate') as $listener) {
//            $repositories->getEventManager()->removeEventListener('preUpdate', $listener);
//        }
//
        echo "$count jobs found, which have to expire ...\n";
        
        $progress     = new ProgressBar(
            new ConsoleAdapter(
                array(
                'elements' => array(
                    ConsoleAdapter::ELEMENT_TEXT,
                    ConsoleAdapter::ELEMENT_BAR,
                    ConsoleAdapter::ELEMENT_PERCENT,
                    ConsoleAdapter::ELEMENT_ETA
                ),
                'textWidth' => 20,
                'barLeftChar' => '-',
                'barRightChar' => ' ',
                'barIndicatorChar' => '>',
                )
            ),
            0,
            count($jobs)
        );

        $i = 0;

        /* @var \Jobs\Entity\Job $job */
        foreach ($jobs as $job) {
            $progress->update($i++, 'Job ' . $i . ' / ' . $count);

            $job->changeStatus('expired');

            if (0 == $i % 500) {
                $progress->update($i, 'Write to database...');
                $repositories->flush();
            }
        }
        $progress->update($i, 'Write to database...');
        $repositories->flush();
        $progress->update($i, 'Done');
        $progress->finish();

        return PHP_EOL;
    }
    
    public function setpermissionsAction()
    {
        $repositories = $this->repositories;
        $repository   = $repositories->get('Jobs/Job');
        $userRep      = $repositories->get('Auth/User');
        $jobs         = $repository->findAll();
        $count        = count($jobs);
        $progress     = new CoreProgressBar($count);
        $i            = 0;
        /* @var Job $job */
        foreach ($jobs as $job) {
            $progress->update($i++, 'Job ' . $i . ' / ' . $count);
            
            $permissions = $job->getPermissions();
            $user        = $job->getUser();
            if (!$user instanceof UserInterface) {
                continue;
            }
            try {
                $group       = $user->getGroup($job->getCompany());
            } catch (\Exception $e) {
                continue;
            }
            if ($group) {
                $permissions->grant($group, 'view');
            }
            foreach ($job->getApplications() as $application) {
                $progress->update($i, 'set app perms...');
                $perms = $application->getPermissions();
                $perms->inherit($permissions);
                $jobUser = $userRep->findOneByEmail($job->getContactEmail());
                if ($jobUser) {
                    $perms->grant($jobUser, 'change');
                }
            }
            if (0 == $i % 500) {
                $progress->update($i, 'write to database...');
                $repositories->flush();
            }
        }
        $progress->update($i, 'write to database...');
        $repositories->flush();
        $progress->finish();
        return PHP_EOL;
    }

    private function listExpiredJobs($jobs)
    {
        /* @var \Jobs\Entity\JobInterface $job */
        foreach ($jobs as $job) {
            $id = $job->getId();
            $org = $job->getOrganization();
            if ($org) {
                $org = $org->getName();
            } else {
                $org = $job->getCompany();
            }
            printf(
                '%s   %s   %s   %-30s   %-20s' . PHP_EOL,
                $id,
                $job->getDatePublishStart()->format('Y-m-d'),
                $job->getDatePublishEnd()->format('Y-m-d'),
                substr($job->getTitle(), 0, 30),
                substr($org, 0, 20)
            );
        }
        return count($jobs) . ' Jobs.';
    }

    public function pushFetchExternalImagesJobAction()
    {
        $this->queue('default')->pushJob(FindJobsWithExternalImageJob::create());

        return 'Job pushed to queue.';
    }
}
