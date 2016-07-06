<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Controller;


use Core\Console\ProgressBar;
use Core\Repository\RepositoryService;
use Jobs\Entity\Job;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Class ConsoleController
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Controller
 */
class ConsoleController extends AbstractActionController
{
    const EVENT_UPDATE_INDEX    = 'solr.console.update_index';

    public function activeJobIndexAction()
    {
        /* @var RepositoryService $repositories */
        /* @var \Jobs\Repository\Job $jobRepo */
        /* @var \Doctrine\ODM\MongoDB\Cursor $jobs */
        /* @var \Solr\Listener\JobEventSubscriber $jobSubscriber */
        $sl = $this->serviceLocator;
        $repositories = $sl->get('repositories');
        $jobRepo = $repositories->get('Jobs/Job');
        $jobSubscriber = $sl->get('Solr/Listener/JobEventSubscriber');

        $jobs = $jobRepo->findActiveJob();
        $count = $jobs->count();

        $progressBar = $this->createProgressBar($count);
        $i = 1;
        foreach($jobs as $job){
            /* @var Job $job */
            $jobSubscriber->consoleIndex($job);
            $progressBar->update($i, 'Job '.$i.' / '.$count);
            $i++;
        }

        return PHP_EOL;
    }
    
    protected function createProgressBar($count)
    {
        return new ProgressBar($count);
    }
}