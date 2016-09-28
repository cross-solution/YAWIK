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
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\Job;
use Solr\Bridge\Manager;
use Solr\Filter\EntityToDocument\Job as EntityToDocumentFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use SolrClient;

/**
 * @author Anthonius Munthi <me@itstoni.com>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.26
 * @package Solr\Listener
 */
class JobEventSubscriber implements EventSubscriber
{

    /**
     * @var Manager
     */
    protected $solrManager;
    
    /**
     * @var EntityToDocumentFilter
     */
    protected $entityToDocumentFilter;

    /**
     * @var SolrClient
     */
    protected $solrClient;
    
    /**
     * @var Job[]
     */
    protected $add = [];
    
    /**
     * @var Job[]
     */
    protected $delete = [];
    
    /**
     * @param Manager $manager
     * @param EntityToDocumentFilter $entityToDocumentFilter
     */
    public function __construct(Manager $manager, EntityToDocumentFilter $entityToDocumentFilter)
    {
        $this->solrManager = $manager;
        $this->entityToDocumentFilter = $entityToDocumentFilter;
    }
    
    /**
     * Define what event this subscriber listen to
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postUpdate,
            Events::postFlush
        ];
    }
    
    /**
     * @param LifecycleEventArgs $eventArgs
     * @since 0.27
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        
        // check for a job instance
        if (!$document instanceof Job) {
            return;
        }
        
        // check if the status has been changed
        if (!$eventArgs->hasChangedField('status')) {
            return;
        }
            
        // check if the job is active
        if ($document->isActive()) {
            // mark it for commit
            $this->add[] = $document;
        } else {
            // mark it for delete
            $this->delete[] = $document;
        }
    }
    
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        // check if there is any job to process
        if (!$this->add && !$this->delete) {
            return;
        }
        
        $client = $this->getSolrClient();
        
        // process jobs for commit
        foreach ($this->add as $job) {
            $document = $this->entityToDocumentFilter->filter($job);
            $client->addDocument($document);
        }
        
        // process jobs for delete
        foreach ($this->delete as $job) {
            $client->deleteByIds($this->entityToDocumentFilter->getDocumentIds($job));
        }
    }
    
    /**
     * @param LifecycleEventArgs $eventArgs
     * @since 0.27
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        // check if there is any job to process
        if (!$this->add && !$this->delete) {
            return;
        }
        
        // commit to index & optimize it
        $client = $this->getSolrClient();
        $client->commit();
        $client->optimize();
    }
    
    /**
	 * @return SolrClient
	 * @since 0.27
	 */
	protected function getSolrClient()
    {
        if (!isset($this->solrClient)) {
            $path = $this->solrManager->getOptions()->getJobsPath();
            $this->solrClient = $this->solrManager->getClient($path);
        }
        
        return $this->solrClient;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return JobEventSubscriber
     */
    static public function factory(ServiceLocatorInterface $serviceLocator)
    {
        return new static($serviceLocator->get('Solr/Manager'), new EntityToDocumentFilter());
    }
}