<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Listener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\Job;
use Solr\Bridge\Manager;
use Solr\Bridge\Util;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class JobEventSubscriber
 *
 * @author  Anthonius Munthi <me@itstoni.com>
 * @since   0.26
 * @package Solr\Event\Listener
 */
class JobEventSubscriber implements EventSubscriber
{

    /**
     * @var Manager
     */
    protected $solrManager;
    /**
     * JobEventSubscriber constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->solrManager = $manager;
    }
    /**
     * Define what event this subscriber listen to
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
            Events::postPersist,
        ];
    }
    public function consoleIndex(Job $job)
    {
        $this->updateIndex($job);
    }
    /**
     * Handle doctrine post persist event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        $this->updateIndex($document);
    }
    /**
     * Handle doctrine postUpdate event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        $this->updateIndex($document);
    }
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    static public function factory(ServiceLocatorInterface $serviceLocator)
    {
        /* @var Manager $manager */
        $manager = $serviceLocator->get('Solr/Manager');
        return new self($manager);
    }
    /**
     * @param $document
     */
    protected function updateIndex($document)
    {
        if(!$document instanceof Job){
            return;
        }
        $solrDoc = $this->generateInputDocument($document, new \SolrInputDocument());
        try{
            $this->solrManager->addDocument($solrDoc,$this->solrManager->getOptions()->getJobsPath());
        }catch (\Exception $e){
            // @TODO: What to do when the process failed?
        }
    }
    /**
     * Generate input document
     *
     * @param   Job                 $job
     * @param   \SolrInputDocument  $document
     * @return  \SolrInputDocument
     */
    public function generateInputDocument(Job $job, $document)
    {
        $document->addField('id',$job->getId());
        $document->addField('entityName','job');
        $document->addField('title',$job->getTitle());
        $document->addField('applicationEmail',$job->getContactEmail());
        if ($job->getLink()) {
            $document->addField('link', $job->getLink());
            $oldErrorReporting=error_reporting(0);
            $dom = new \DOMDocument();
            $dom->loadHTML($job->getLink());
            error_reporting($oldErrorReporting);
            $document->addField('html',$dom->saveHTML());
        }
        if($job->getDateCreated()){
            $document->addField('dateCreated',Util::convertDateTime($job->getDateCreated()));
        }
        if($job->getDateModified()){
            $document->addField('dateModified',Util::convertDateTime($job->getDateModified()));
        }
        if($job->getDatePublishStart()){
            $document->addField('datePublishStart',Util::convertDateTime($job->getDatePublishStart()));
        }
        if($job->getDatePublishEnd()){
            $document->addField('datePublishEnd',Util::convertDateTime($job->getDatePublishEnd()));
        }
        $document->addField('isActive',$job->isActive());
        $document->addField('lang',$job->getLanguage());
        $this->processLocation($job,$document);
        if(!is_null($job->getOrganization())){
            try {
                $this->processOrganization($job,$document);
            }catch (\Exception $e){
                // @TODO: What to do when the process failed?
            }
        }
        return $document;
    }
    /**
     * Processing organization part
     *
     * @param Job                   $job
     * @param \SolrInputDocument    $document
     */
    public function processOrganization(Job $job,$document)
    {
        if(!is_null($job->getOrganization()->getImage())){
            $uri = $job->getOrganization()->getImage()->getUri();
            $document->addField('companyLogo',$uri);
        }
        $document->addField('organizationName',$job->getOrganization()->getOrganizationName()->getName());
        $document->addField('organizationId',$job->getOrganization()->getId());
    }

    /**
     * Processing location part
     * @param Job                $job
     * @param \SolrInputDocument $document
     */
    public function processLocation(Job $job,$document)
    {
        /* @var \Jobs\Entity\Location $location */
        foreach($job->getLocations() as $location){

            $loc = new \SolrInputDocument();
            $loc->addField('entityName', 'location');
            if(is_object($location->getCoordinates())){
                $coordinate = Util::convertLocationCoordinates($location);
                $loc->addField('point', $coordinate);
                $loc->addField('latLon', $coordinate);
                $document->addField('locations', $coordinate);
                $document->addField('points', $coordinate);
                $loc->addField('id', $job->getId() . '-' . $coordinate);
                $loc->addField('city', $location->getCity());
                $loc->addField('country', $location->getCountry());
                $loc->addField('region', $location->getRegion());
                $loc->addField('postalCode', $location->getPostalCode());
                $document->addField('regionList',$location->getRegion());
                $document->addChildDocument($loc);
            }
        }
        $document->addField('location',$job->getLocation());
    }
}