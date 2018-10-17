<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Exception\ImmutablePropertyException;

/**
 * Class SnapshotMeta
 *
 * @ODM\EmbeddedDocument
 * @ODM\HasLifecycleCallbacks
 */
class SnapshotMeta implements
    ModificationDateAwareEntityInterface,
                              DraftableEntityInterface,
                              Status\StatusAwareEntityInterface
{
    use ModificationDateAwareEntityTrait, DraftableEntityTrait, Status\StatusAwareEntityTrait;
}
