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

use Core\Entity\SnapshotMeta;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class JobSnapshotMeta
 * @package Jobs\Entity
 *
 * @ODM\EmbeddedDocument
 * @ODM\HasLifecycleCallbacks
 */
class JobSnapshotMeta extends SnapshotMeta
{

    const STATUS_ENTITY_CLASS = JobSnapshotStatus::class;

    /**
     * @var Job
     * @ODM\EmbedOne(targetDocument="JobSnapshot")
     */
    protected $entity;

    public function __construct()
    {
        $this->setStatus(JobSnapshotStatus::ACTIVE);
    }
}
