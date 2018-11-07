<?php

/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Applications\Controller;

use Applications\Entity\Application;
use Applications\Repository\Application as ApplicationRepository;
use Core\Repository\RepositoryService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Interop\Container\ContainerInterface;
use Zend\Filter\FilterPluginManager;
use Zend\Mvc\Controller\AbstractActionController;
use Core\Console\ProgressBar;
use Zend\View\Model\ViewModel;
use \Zend\Text\Table\Table;
use \Zend\Text\Table\Row;
use \Zend\Text\Table\Column;

/**
 * Handles cli actions for applications
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class ConsoleController extends AbstractActionController
{
    /**
     * @var RepositoryService
     */
    private $repositories;
    
    /**
     * @var FilterPluginManager
     */
    private $filterManager;
    
    /**
     * @var array
     */
    private $config;
    
    /**
     * @var DocumentManager
     */
    private $documentManager;
    
    public function __construct(
        RepositoryService $repositories,
        FilterPluginManager $filterManager,
        DocumentManager $documentManager,
        $config
    ) {
        $this->repositories = $repositories;
        $this->filterManager = $filterManager;
        $this->documentManager = $documentManager;
        $this->config = $config;
    }
    
    public static function factory(ContainerInterface $container)
    {
        return new self(
            $container->get('repositories'),
            $container->get('FilterManager'),
            $container->get('Core/DocumentManager'),
            $container->get('Config')
        );
    }

    /**
     * regenerate keywords for applications
     *
     * @return string
     */
    public function generateKeywordsAction()
    {
        $applications = $this->fetchApplications();
        $count        = count($applications);
        $repositories = $this->repositories; //->get('Applications/Application');

        if (0 === $count) {
            return 'No applications found.';
        }
        
        // preUpdate includes a modified date, and we don't want that
        foreach ($repositories->getEventManager()->getListeners('preUpdate') as $listener) {
            $repositories->getEventManager()->removeEventListener('preUpdate', $listener);
        }
                
        echo "Generate keywords for $count applications ...\n";
        
        $progress     = new ProgressBar($count);

        /** @var \Core\Repository\Filter\PropertyToKeywords $filter */
        $filter = $this->filterManager->get('Core/Repository/PropertyToKeywords');
        $i = 0;

        /** @var \Applications\Entity\Application $application */
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
        $count = count($applications);
        $i=0;
        echo "Calculate rating for " . $count . " applications ...\n";
        
        $progress = new ProgressBar($count);
        /** @var  \Applications\Entity\Application $application */
        foreach ($applications as $application) {
            $progress->update($i++, 'Application ' . $i . ' / ' . $count);
            $application->getRating(/* recalculate */ true);
        }
        $progress->update($i, 'Write to database...');
        $this->repositories->flush();
        $progress->finish();
        
        return PHP_EOL;
    }

    /**
     * removes unfinished applications. Applications, which are in Draft Mode for
     * more than 24 hours.
     */
    protected function cleanupAction()
    {
        $days = 2;
        $date = new \DateTime();
        $date->modify("-$days day");

        $filter = array("before" => $date->format("Y-m-d"),
                        "isDraft" => 1);
        
        $applications    = $this->fetchApplications($filter);
        $documentManager = $this->documentManager;

        $count = count($applications);
        $i=0;

        echo  $count . " applications in Draft Mode and older than " . $days . " days will be deleted\n";

        $progress = new ProgressBar($count);

        foreach ($applications as $application) {
            $progress->update($i++, 'Application ' . $i . ' / ' . $count);
            try {
                $documentManager->remove($application);
            } catch (\Exception $e) {
                // log something
                $e->getCode();
            }
        }
        $progress->update($i, 'clean up database');
        $documentManager->flush();
        $progress->finish();
    }

    /**
     * list available view scripts
     */
    public function listviewscriptsAction()
    {
        $config = $this->config;

        $table = new Table(
            array('columnWidths' => array(40, 40, 40),
                                 'decorator' => 'ascii')
        );

        $table->appendRow(array('Module', 'Name', 'Description'));

        $offset=strlen(getcwd())+1;
        $links="";
        $github='https://github.com/cross-solution/YAWIK/blob/master/';

        foreach ($config['view_manager']['template_map'] as $key => $absolute_filename) {
            // strip the application_root plus an additional slash
            $filename=substr(realpath($absolute_filename), $offset);
            if (preg_match('~module/([^/]+)~', $filename, $match)) {
                $module=$match[1];
            } else {
                $module="not found ($key)";
            }

            $viewModel = new ViewModel();
            $viewModel->setTemplate($key);

            $row = new Row();
            $row->appendColumn(new Column($module));
            if ($filename) {
                $row->appendColumn(new Column('`' . $key . '`_'));
                $links.='.. _'. $key .': '. $github.$filename .PHP_EOL;
            } else {
                $row->appendColumn(new Column("WRONG CONFIGURATION"));
            }
            $comment="";
            if (file_exists($absolute_filename)) {
                $src=file_get_contents($absolute_filename);
                $comment="file exists";
                if (preg_match("/{{rtd:\s*(.*)}}/", $src, $match)) {
                    $comment=$match['1'];
                }
            }
            $row->appendColumn(new Column($comment));
            $table->appendRow($row);
        }

        echo $table.PHP_EOL;
        echo $links;

        return PHP_EOL;
    }

    public function resetFilesPermissionsAction()
    {
        echo "Loading applications... ";

        $filter       = \Zend\Json\Json::decode($this->params('filter', '{}'), \Zend\Json\Json::TYPE_ARRAY);
        $filter['$or'] = array(
            array('attachments' => array('$exists' => 1)),
            array('contact.image' => array('$exists' => 1)),
        );
        $applications = $this->fetchApplications($filter);
        $count        = count($applications);

        echo "[DONE] -> $count applications found.\n";

        if (!$count) {
            return;
        }
        $progress = new ProgressBar($count);
        $i=0;

        foreach ($applications as $app) {
            $progress->update($i++, "Process $i / $count");

            $permissions = $app->getPermissions();
            $resources = $permissions->getResources();
            foreach ($resources as $r) {
                if ($r instanceof \Auth\Entity\GroupInterface) {
                    $permissions->revoke($r);
                }
            }

            $attachments = $app->getAttachments();
            foreach ($attachments as $attachment) {
                $attachment->getPermissions()
                           ->clear()
                           ->inherit($permissions);
            }
            $contact = $app->getContact();
            if ($contact) {
                $image = $contact->getImage();

                if ($image) {
                    $image->getPermissions()
                          ->clear()
                          ->inherit($permissions);
                }
            }
        }
        $progress->update($i, '[DONE]');
        echo "Flushing...";
        $repos = $this->repositories;
        $repos->flush();

        echo " [DONE]\n";
    }

    /**
     * Fetches applications
     *
     * @param array $filter
     * @return Application[]
     */
    protected function fetchApplications($filter = array())
    {
        $repositories = $this->repositories;

        /** @var ApplicationRepository $appRepo */
        $appRepo      = $repositories->get('Applications/Application');
        $query        = array('isDraft' => null);

        foreach ($filter as $key => $value) {
            switch ($key) {
                case "before":
                    $date = new \DateTime($value);
                    $q = array('$lt' => $date);
                    if (isset($query['dateCreated.date'])) {
                        $query['dateCreated.date'] = array_merge(
                            $query['dateCreated.date'],
                            $q
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
                            $query['dateCreated.date'],
                            $q
                        );
                    } else {
                        $query['dateCreated.date'] = $q;
                    }
                    break;

                case "id":
                    $query['_id'] = new \MongoId($value);
                    break;
                case "isDraft":
                    $query['isDraft'] = (bool) $value;
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
