<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Event;


use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Jobs\Entity\JobInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobEventSubscriber implements EventSubscriber
{
    /**
     * @var \SolrClient
     */
    protected $solrClient;

    /**
     * JobEventSubscriber constructor.
     */
    public function __construct(\SolrClient $solrClient)
    {
        $this->solrClient = $solrClient;
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

        if (!$document instanceof JobInterface) {
            return;
        }

        $solrDoc = new \SolrInputDocument();
        $solrDoc->addField('id', $document->getId());
        $solrDoc->addField('applyId', $document->getApplyId());
        $solrDoc->addField('title', $document->getTitle());
        $solrDoc->addField('organization', $document->getOrganization());


        $client = $this->solrClient;
        $client->addDocument($solrDoc);
        $client->commit();
        $client->optimize();
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceof JobInterface) {
            return;
        }

        $solrDoc = new \SolrInputDocument();
        $solrDoc->addField('id', $document->getId());
        $solrDoc->addField('applyId', $document->getApplyId());
        $solrDoc->addField('title', $document->getTitle());
        $solrDoc->addField('organization', $document->getOrganization());

        $client = $this->solrClient;
        $client->addDocument($solrDoc);
        $client->commit();
        $client->optimize();
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    static public function factory(ServiceLocatorInterface $serviceLocator)
    {
        $client = $serviceLocator->get('Solr/Client');
        return new static($client);
    }
}