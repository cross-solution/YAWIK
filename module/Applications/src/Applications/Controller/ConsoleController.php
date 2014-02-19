<?php

/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** ActionController of Core */
namespace Applications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Stdlib\Parameters;
use Zend\Mvc\MvcEvent;
use Zend\Console\Request as ConsoleRequest;
use Zend\ProgressBar\ProgressBar;
use Zend\ProgressBar\Adapter\Console as ConsoleAdapter;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.  
 *
 */
class ConsoleController extends AbstractActionController {

    public function generateKeywordsAction() {
        
        $services     = $this->getServiceLocator();
        $repositories = $services->get('repositories');
        $appRepo      = $repositories->get('Applications/Application');
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
                    if (isset($query['dateCreated.date'])) {
                        $query['dateCreated.date']= array_merge(
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
                        $query['dateCreated.date']= array_merge(
                            $query['dateCreated.date'], $q
                        );
                    } else {
                        $query['dateCreated.date'] = $q;
                    }
                    break;
                                        
                default:
                    $query[$key] = $value;
                    break;
            }
        }
        
        $applications = $appRepo->findBy($query);
        $applications->limit($limit);
        $count        = $applications->count(true);

        if (0 === $count) {
            return 'No applications found.';
        }
        
        foreach ($repositories->getEventManager()->getListeners('preUpdate') as $listener) {
            $repositories->getEventManager()->removeEventListener('preUpdate', $listener);
        }
                
        echo "Generate keywords for $count applications ...\n";
        
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
            0, count($applications)
        );
        
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
        $progress->update($i, 'Done');
        $progress->finish();
        
        return PHP_EOL;   
    }
}

