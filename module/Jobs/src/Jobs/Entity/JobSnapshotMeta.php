<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
 * @ODM\Document(collection="jobs.snapshots", repositoryClass="Jobs\Repository\JobSnapshotMeta")
 * @ODM\HasLifecycleCallbacks
 */
class JobSnapshotMeta extends SnapshotMeta {

    /**
     * @var Job
     * @ODM\EmbedOne(targetDocument="JobSnapshot")
     */
    protected $entity;

} 