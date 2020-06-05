<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace Jobs\Entity;

use Core\Entity\Status\AbstractStatus;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Status of an Job Snapshot
 *
 * @ODM\EmbeddedDocument
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class JobSnapshotStatus extends AbstractStatus
{
    const ACTIVE = "active";
    const ACCEPTED = "accepted";
    const REJECTED = "rejected";
}
