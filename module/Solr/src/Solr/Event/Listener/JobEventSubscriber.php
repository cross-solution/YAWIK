<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Event\Listener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\Job;
use Solr\Bridge\Manager;
use Zend\ServiceManager\ServiceLocatorInterface;

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

    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();

        if (!$document instanceof Job) {
            return;
        }

        $solrDoc = $this->generateInputDocument($document, new \SolrInputDocument());
        $client = $this->solrManager->getClient('/solr/YawikJobs');
        $client->addDocument($solrDoc);
        $client->commit();
        $client->optimize();
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceof Job) {
            return;
        }

        $solrDoc = $this->generateInputDocument($document,new \SolrInputDocument());
        $client = $this->solrManager->getClient('/solr/YawikJobs');
        try{
            $client->addDocument($solrDoc);
            $client->commit();
            $client->optimize();
        }catch (\Exception $e){
            throw $e;
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
     * @param   Job                 $job
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

    public function processOrganization(Job $job,$document)
    {
        if(!is_null($job->getOrganization()->getImage())){
            $uri = $job->getOrganization()->getImage()->getUri();
            $document->addField('companyLogo',$uri);
        }
        $document->addField('organizationName',$job->getOrganization()->getOrganizationName()->getName());
        // @TODO: uncomment this when organization id is fix
        //$document->addField('organizationId',$job->getOrganization()->getId());
    }

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