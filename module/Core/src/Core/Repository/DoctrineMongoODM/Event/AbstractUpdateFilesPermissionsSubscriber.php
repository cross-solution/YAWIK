<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Repository\DoctrineMongoODM\Event;

use Core\Entity\PermissionsAwareInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Zend\Stdlib\ArrayUtils;

/**
 * Boilerplate for Files permissions update subscriber.
 *
 * updates the permissions of embedded files if the permissions on the parent document
 * had changed.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.26
 */
abstract class AbstractUpdateFilesPermissionsSubscriber implements EventSubscriber
{
    /**
     * List of properties where embedded files can be retreived.
     *
     * Note: entries will be prepended with "get"
     *
     * @var string[]
     */
    protected $filesProperties = [];

    /**
     * The document class that should be watched.
     *
     * @var string
     */
    protected $targetDocument;


    public function getSubscribedEvents()
    {
        return [ Events::onFlush ];
    }

    /**
     * Updates permissions on embedded files.
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        $filter = function($element) {
            return $element instanceOf $this->targetDocument
                   && $element instanceOf PermissionsAwareInterface
                   && $element->getPermissions()->hasChanged();
        };

        $inserts = array_filter($uow->getScheduledDocumentInsertions(), $filter);
        $updates = array_filter($uow->getScheduledDocumentUpdates(), $filter);

        $this->process($inserts);
        $this->process($updates, $dm, $uow);
    }

    /**
     *
     *
     * @param PermissionsAwareInterface[] $documents
     * @param null|DocumentManager $dm
     * @param null|UnitOfWork $uow
     */
    protected function process($documents, $dm = null, $uow = null)
    {
        foreach ($documents as $document) {
            $perms = $document->getPermissions();
            $files = $this->getFiles($document);

            foreach ($files as $file) {
                $file
                    ->getPermissions()
                    ->clear()
                    ->inherit($perms);

                if ($dm) {
                    $uow->computeChangeSet(
                        $dm->getClassMetadata(get_class($file)),
                        $file
                    );
                }
            }
        }
    }

    /**
     *
     *
     * @param \Core\Entity\EntityInterface $document
     *
     * @return PermissionsAwareInterface[]
     */
    protected function getFiles($document)
    {
        $files = [];

        foreach ($this->filesProperties as $prop) {
            $getter = "get$prop";
            $file   = $document->$getter();
            if ($file instanceof Collection) {
                $files = array_merge($files, ArrayUtils::iteratorToArray($file));

            } else {
                $files[] = $file;
            }
        }

        return array_filter($files, function($i) { return $i instanceOf PermissionsAwareInterface; });
    }
}