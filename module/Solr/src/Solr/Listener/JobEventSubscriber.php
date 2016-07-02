<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Listener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\Job;
use Solr\Bridge\Manager;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class JobEventSubscriber
 *
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

    /**
     * Handle doctrine post persist event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();

        if (!$document instanceof Job) {
            return;
        }

        $solrDoc = $this->generateInputDocument($document, new \SolrInputDocument());
        try{
            $this->solrManager->addDocument($solrDoc,'/solr/YawikJobs');
        }catch (\Exception $e){
            // @TODO: What to do when the process failed?
        }
    }

    /**
     * Handle doctrine postUpdate event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceof Job) {
            return;
        }

        $solrDoc = $this->generateInputDocument($document,new \SolrInputDocument());
        try{
            $this->solrManager->addDocument($solrDoc,'/solr/YawikJobs');
        }catch (\Exception $e){
            // @TODO: What to do when the process failed?
        }
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    static public function factory(ServiceLocatorInterface $serviceLocator)
    {
        $manager = $serviceLocator->get('Solr/Manager');
        return new static($manager);
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
        $document->addField('title',$job->getTitle());
        $document->addField('applicationEmail',$job->getContactEmail());

        if($job->getDateCreated()){
            $document->addField('dateCreated',
                $job->getDateCreated()->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT)
            );
        }
        if($job->getDateModified()){
            $document->addField('dateModified',
                $job->getDateModified()->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT)
            );
        }
        if($job->getDatePublishStart()){
            $document->addField('datePublishStart',
                $job->getDatePublishStart()->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT)
            );
        }

        if($job->getDatePublishEnd()){
            $document->addField('datePublishEnd',
                $job->getDatePublishEnd()->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT)
            );
        }

        $document->addField('isActive',$job->isActive());
        $document->addField('lang',$job->getLanguage());

        $this->processLocation($job,$document);
        if(!is_null($job->getOrganization())){
            $this->processOrganization($job,$document);
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
            $coord = $location->getCoordinates()->getCoordinates();
            $document->addField('latLon',doubleval($coord[0]).','.doubleval($coord[1]));
            $document->addField('postCode',$location->getPostalCode());
            $document->addField('regionText',$location->getRegion());
        }

        $document->addField('location',$job->getLocation());
    }
}