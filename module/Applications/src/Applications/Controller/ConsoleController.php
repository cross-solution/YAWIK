<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use Core\Console\ProgressBar;

/**
 * Handles cli actions for applications 
 */
class ConsoleController extends AbstractActionController {

    /**
     * regenarate keywords for applications
     * 
     * @return string
     */
    public function generateKeywordsAction() {
        
        $services     = $this->getServiceLocator();
        $applications = $this->fetchApplications();
        $count        = count($applications);
        $repositories = $services->get('repositories'); //->get('Applications/Application');

        if (0 === $count) {
            return 'No applications found.';
        }
        
        // preUpdate includes a modified date, and we don't want that
        foreach ($repositories->getEventManager()->getListeners('preUpdate') as $listener) {
            $repositories->getEventManager()->removeEventListener('preUpdate', $listener);
        }
                
        echo "Generate keywords for $count applications ...\n";
        
        $progress     = new ProgressBar($count);
        
        $filter = $services->get('filtermanager')->get('Core/Repository/PropertyToKeywords');
        $i = 0;
        
        foreach ($applications as $application) {
            $progress->update($i++, 'Application ' . $i . ' / ' . $count);
            $keywords = $filter->filter($application);
            
            $application->setKeywords($keywords);
            
            if (0 == $i % 500) {
                $progress->update($i, 'Write to database...');
                $repositories->flush();
            }
        }
        $progress->update($i, 'Write to database...');
        $repositories->flush();
        $progress->finish();
        
        return PHP_EOL;   
    }
    
    /**
     * Recalculates ratings for applications
     * 
     * @return string
     */
    public function calculateRatingAction()
    {
        $applications = $this->fetchApplications();
        $count = count($applications); $i=0;
        echo "Calculate rating for " . $count . " applications ...\n";
        
        $progress = new ProgressBar($count);
        
        foreach ($applications as $application) {
            $progress->update($i++, 'Application ' . $i . ' / ' . $count);
            $application->getRating(/* recalculate */ true);
        }
        $progress->update($i, 'Write to database...');
        $this->getServiceLocator()->get('repositories')->flush();
        $progress->finish();
        
        return PHP_EOL;
    }

    /**
     * removes unfinished applications. Applications, which are in Draft Mode for
     * more than 24 hours.
     */
    protected function cleanupAction() {
        $days = 6;
        $date = new \DateTime();
        $date->modify("-$days day");

        $filter = array("before" => $date->format("Y-m-d"),
                        "isDraft" => 1);

        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $applications = $this->fetchApplications($filter);

        $count = count($applications); $i=0;

        echo  $count . " applications in Draft Mode and older than " . $days . " days will be deleted\n";

        $progress = new ProgressBar($count);

        foreach ($applications as $application) {
            $progress->update($i++, 'Application ' . $i . ' / ' . $count);
            $repositories->remove($application);
        }
        $progress->finish();
    }
    
    /**
     * Fetches applications
     * 
     * @param Array $filter
     * @return $applications
     */
    protected function fetchApplications($filter = array())
    {
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $appRepo      = $repositories->get('Applications/Application');
        $query        = array();
        $limit        = 0;
        foreach ($filter as $key => $value) {
            switch ($key) {
                case "before":
                    $date = new \DateTime($value);
                    $q = array('$lt' => $date);
                    if (isset($query['dateCreated.date'])) {
                        $query['dateCreated.date'] = array_merge(
                            $query['dateCreated.date'], $q
                        );
                    } else {
                        $query['dateCreated.date'] = $q;
                    }
                    break;

                case "after":
                    $date = new \DateTime($value);
                    $q = array('$gt' => $date);
                    if (isset($query['dateCreated.date'])) {
                        $query['dateCreated.date'] = array_merge(
                            $query['dateCreated.date'], $q
                        );
                    } else {
                        $query['dateCreated.date'] = $q;
                    }
                    break;

                case "id":
                    $query['_id'] = new \MongoId($value);
                    //$query['id'] = $value;
                    break;
                case "isDraft":
                    $query['isDraft'] = true;
                    break;
                default:
                    $query[$key] = $value;
                    break;
            }
        }
        
        $applications = $appRepo->findBy($query);

        return $applications;
    }

}

