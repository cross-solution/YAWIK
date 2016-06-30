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

        $solrDoc = $this->configureSolrInputDocument($document);
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

        $solrDoc = $this->configureSolrInputDocument($document);
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
    protected function configureSolrInputDocument(Job $job)
    {
        $document = new \SolrInputDocument();

        $document->addField('id',$job->getId());
        $document->addField('applicationEmail',$job->getContactEmail());
        if(!is_null($job->getOrganization())){
            $document->addField('companyLogo',$job->getOrganization()->getOrganizationName()->getName());
        }
        $document->addField('title',$job->getTitle());

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
        if($job->getDatePublishStart() instanceof \DateTime){
            $document->addField('datePublishEnd',
                $job->getDatePublishStart()->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT)
            );
        }

        if($job->getDatePublishEnd() instanceof \DateTime){
            $document->addField('datePublishEnd',
                $job->getDatePublishEnd()->setTimezone(new \DateTimeZone('UTC'))->format(Manager::SOLR_DATE_FORMAT)
            );
        }

        foreach($job->getPortals() as $portal){
            $document->addField('portalList',$portal->getId());
        }

        if(method_exists($job,'isActive')){
            $document->addField('isActive',$job->isActive());
        }
        $document->addField('lang',$job->getLanguage());

        if(is_object($job->getOrganization())){
            $document->addField('organizationName',$job->getOrganization()->getOrganizationName()->getName());
        }

        return $document;
    }
}