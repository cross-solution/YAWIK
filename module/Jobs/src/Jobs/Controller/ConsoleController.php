<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Jobs\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;
use Jobs\Entity\Job;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use Zend\ProgressBar\ProgressBar;
use Core\Console\ProgressBar as CoreProgressBar;
use Zend\ProgressBar\Adapter\Console as ConsoleAdapter;
use Auth\Entity\UserInterface;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class ConsoleController extends AbstractActionController {

    public function generateKeywordsAction() {
        
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $jobsRepo     = $repositories->get('Jobs/Job');
        $filter       = \Zend\Json\Json::decode($this->params('filter', '{}'));
        $query        = array();
        $limit        = 0;
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "limit":
                    $limit = $value;
                    break;
                    
                case "before":
                    $date = new \DateTime($value);
                    $q = array('$lt' => $date);
                    if (isset($query['datePublishStart.date'])) {
                        $query['datePublishStart.date']= array_merge(
                                $query['datePublishStart.date'], $q
                        );
                    } else {
                        $query['datePublishStart.date'] = $q;
                    }
                    break;
                    
                case "after":
                    $date = new \DateTime($value);
                    $q = array('$gt' => $date);
                    if (isset($query['datePublishStart.date'])) {
                        $query['datePublishStart.date']= array_merge(
                            $query['datePublishStart.date'], $q
                        );
                    } else {
                        $query['datePublishStart.date'] = $q;
                    }
                    break;
                    
                case "title":
                    $query['title'] = '/' == $value{0}
                                    ? new \MongoRegex($value)
                                    : $value;
                    break;
                    
                default:
                    $query[$key] = $value;
                    break;
            }
        }
        
        $jobs         = $jobsRepo->findBy($query);
        $jobs->limit($limit);
        $count        = $jobs->count(true);

        if (0 === $count) {
            return 'No jobs found.';
        }
        
        foreach ($repositories->getEventManager()->getListeners('preUpdate') as $listener) {
            $repositories->getEventManager()->removeEventListener('preUpdate', $listener);
        }
        
        
        echo "Generate keywords for $count jobs...\n";
        
        $progress     = new ProgressBar(
            new ConsoleAdapter(array(
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
            )),
            0, count($jobs)
        );
        
        $filter = $services->get('filtermanager')->get('Core/Repository/PropertyToKeywords');
        $i = 0;
        
        foreach ($jobs as $job) {
            $progress->update($i++, 'Job ' . $i . ' / ' . $count);
            $keywords = $filter->filter($job);
            
            $job->setKeywords($keywords);
            
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
        foreach ($jobs as $job) {
            $progress->update($i++, 'Job ' . $i . ' / ' . $count);
            
            $permissions = $job->getPermissions();
            $user        = $job->getUser();
            if (!$user instanceof UserInterface) { continue; }
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

