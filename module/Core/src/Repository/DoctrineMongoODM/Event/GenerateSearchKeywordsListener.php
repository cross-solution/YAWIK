<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** RepositoryCreated.php */
namespace Core\Repository\DoctrineMongoODM\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Core\Entity\SearchableEntityInterface;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Core\Repository\Filter\PropertyToKeywords;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Zend\Filter\FilterInterface;

class GenerateSearchKeywordsListener implements EventSubscriber
{
    protected $keywordsFilter;
    
    public function getKeywordsFilter()
    {
        if (!$this->keywordsFilter) {
            $this->setKeywordsFilter(new PropertyToKeywords());
        }
        return $this->keywordsFilter;
    }
    
    public function setKeywordsFilter(FilterInterface $filter)
    {
        $this->keywordsFilter = $filter;
        return $this;
    }
    
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceof SearchableEntityInterface) {
            return;
        }
        
        $filter   = $this->getKeywordsFilter();
        $keywords = $filter->filter($document);
        $document->setKeywords($keywords);
    }
    
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceof SearchableEntityInterface) {
            return;
        }
        
        
        $dm         = $eventArgs->getDocumentManager();
        $uow       = $dm->getUnitOfWork();
        $changeset = $uow->getDocumentChangeset($document);
        $filter    = $this->getKeywordsFilter();
        $keywords  = array();

        $mustUpdate = false;
        foreach ($document->getSearchableProperties() as $name) {
            if (isset($changeset[$name])) {
                $mustUpdate = true;
                break;
            }
        }
        
        if (!$mustUpdate) {
            return;
        }
        
        $keywords = $filter->filter($document);
        $document->setKeywords($keywords);
        $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($document)), $document);
    }
    
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate, Events::prePersist);
    }
}
