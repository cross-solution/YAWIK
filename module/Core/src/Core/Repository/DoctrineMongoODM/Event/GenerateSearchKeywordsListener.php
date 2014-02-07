<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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
    
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if (!$document instanceOf SearchableEntityInterface) {
            return;
        }
        
        
        $dm         = $eventArgs->getDocumentManager();
        $uow       = $dm->getUnitOfWork();
        $changeset = $uow->getDocumentChangeset($document);
        $filter    = $this->getKeywordsFilter();
        $properties= $document->getSearchableProperties();
        $keywords  = array();

        $mustUpdate = false;
        foreach ($properties as $name) {
            if (isset($changeset[$name])) {
                $mustUpdate = true;
                break;
            }
        }
        
        if (!$mustUpdate) {
            return;
        }
        
        foreach ($properties as $name) {
            $keywords = array_merge($keywords, $filter->filter($document->$name));
        }
        
        $keywords = array_unique($keywords);
        $document->setKeywords($keywords);
        $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($document)), $document);
        
    }
    
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate);
    }
	

}

