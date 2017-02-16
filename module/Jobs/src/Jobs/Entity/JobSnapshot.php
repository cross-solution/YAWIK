<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\Collection\ArrayCollection;
use Core\Entity\DraftableEntityInterface;
use Core\Entity\MetaDataProviderTrait;
use Core\Entity\Permissions;
use Core\Entity\Snapshot as BaseEntity;
use Auth\Entity\UserInterface;
use Core\Entity\SnapshotAttributesProviderInterface;
use Core\Entity\SnapshotTrait;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Exception;
use InvalidArgumentException;
use Organizations\Entity\OrganizationInterface;
use Core\Exception\ImmutablePropertyException;
use Core\Entity\PermissionsInterface;
use Organizations\Entity\Organization;
use Core\Entity\SnapshotInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Entity\EntityInterface;

/**
 * by using the BaseEntity,
 *
 * Class JobSnapshot
 * @package Jobs\Entity
 *
 * @ODM\Document(collection="jobs.snapshots2", repositoryClass="Core\Repository\SnapshotRepository")
 */
class JobSnapshot extends Job implements SnapshotInterface, SnapshotAttributesProviderInterface
{
    use SnapshotTrait;

    protected $snapshotAttributes = [
        'title', 'company', 'organization', 'contactEmail', 'language',
        'location', 'locations', 'link', 'datePublishStart', 'datePublishEnd',
        'reference', 'atsEnabled', 'template', 'uriApply', 'templateValues',
        'classifications', 'atsMode', 'metaData',
    ];

    public function getId()
    {
        return $this->proxy('getId');
    }

    public function getSnapshotId()
    {
        return $this->id;
    }

    public function getResourceId()
    {
        return $this->proxy('getResourceId');
    }

    public function setApplyId($applyId)
    {
        $this->immutable('applyId');
    }

    public function getApplyId()
    {
        return $this->proxy('getApplyId');
    }

    public function setUser(UserInterface $user)
    {
        $this->immutable('user');
    }

    public function getUser()
    {
        return $this->proxy('getUser');
    }

    public function unsetUser($removePermissions = true)
    {
        $this->immutable('user');
    }

    public function setApplications(Collection $applications)
    {
        $this->immutable('applications');
    }

    public function getApplications()
    {
        return $this->proxy('getApplications');
    }

    public function getUnreadApplications()
    {
        return $this->proxy('getUnreadApplications');
    }


    public function changeStatus($status, $message = '[System]')
    {
        $this->immutable('status');
    }

    public function getStatus()
    {
        return $this->proxy('getStatus');
    }

    public function setStatus($status)
    {
        $this->immutable('status');
    }

    public function setHistory(Collection $history)
    {
        $this->immutable('history');
    }

    public function getHistory()
    {
        return $this->proxy('getHistory');
    }

    public function setTermsAccepted($termsAccepted)
    {
        $this->immutable('termsAccepted');
    }

    public function getTermsAccepted()
    {
        return $this->proxy('getTermsAccepted');
    }

    public function getUriPublisher()
    {
        return $this->proxy('getUriPublisher');
    }

    public function setUriPublisher($uriPublisher)
    {
        $this->immutable('uriPublisher');
    }

    public function getPublisher($key)
    {
        $this->inaccessible('publisher');
    }

    public function setPublisherReference($key, $reference)
    {
        $this->immutable('publisherReference');
    }

    public function getPermissions()
    {
        if (!$this->permissions) {
            $originalPermissions = $this->getSnapshotMeta()->getEntity()->getPermissions();
            $permissions = new Permissions('Job/Permissions');
            $permissions->inherit($originalPermissions);
            $this->permissions = $permissions;
        }

        return clone $this->permissions;
    }

    public function setPermissions(PermissionsInterface $permissions)
    {
        $this->immutable('permissions');
    }

    public function setPortals(array $portals)
    {
        $this->immutable('portals');
    }

    public function getPortals()
    {
        return $this->proxy('getPortals');
    }

    public function isActive()
    {
        return $this->proxy('isActive');
    }

    public function getDateCreated()
    {
    }

    public function setDateCreated($dateCreated = null)
    {
    }

    public function getDateModified()
    {
    }

    public function setDateModified($dateModified = null)
    {
    }
}
