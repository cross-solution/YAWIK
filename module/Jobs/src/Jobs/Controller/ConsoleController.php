<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ConsoleController of Jobs */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Jobs\Entity\Job;
use Zend\Console\Request as ConsoleRequest;
use Zend\ProgressBar\ProgressBar;
use Core\Console\ProgressBar as CoreProgressBar;
use Zend\ProgressBar\Adapter\Console as ConsoleAdapter;
use Auth\Entity\UserInterface;

class ConsoleController extends AbstractActionController
{

    public function expireJobsAction()
    {
        
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        /* @var \Jobs\Repository\Job $jobsRepo */
        $jobsRepo     = $repositories->get('Jobs/Job');
        $filter       = \Zend\Json\Json::decode($this->params('filter', '{}'));
        $query        = array();
        $limit        = 10;
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "limit":
                    $limit = $value;
                    break;
                    
                case "days":
                    $date = new \DateTime();
                    $date->modify('-' . (int) $value. ' day');
                    $q = array('$lt' => $date);
                    if (isset($query['datePublishStart.date'])) {
                        $query['datePublishStart.date']= array_merge(
                            $query['datePublishStart.date'],
                            $q
                        );
                    } else {
                        $query['datePublishStart.date'] = $q;
                    }
                    break;
                    
                default:
                    $query[$key] = $value;
                    break;
            }
        }
        $query['status.name'] = 'active';

        $jobs = $jobsRepo->findBy($query,null,$limit);
        $count = count($jobs);

        if (0 === $count) {
            return 'No jobs found.';
        }
        
        foreach ($repositories->getEventManager()->getListeners('preUpdate') as $listener) {
            $repositories->getEventManager()->removeEventListener('preUpdate', $listener);
        }
        
        
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
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
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
}
