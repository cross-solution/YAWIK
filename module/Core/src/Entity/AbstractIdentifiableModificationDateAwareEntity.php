<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core Entities */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Abstract Identifiable Modification Date Aware Entity
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @ODM\MappedSuperclass @ODM\HasLifecycleCallbacks
 */
abstract class AbstractIdentifiableModificationDateAwareEntity extends AbstractIdentifiableEntity implements ModificationDateAwareEntityInterface
{
    use ModificationDateAwareEntityTrait;
}
