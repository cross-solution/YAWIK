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

/**
 * Interface SnapshotInterface
 *
 * just for identification if a entity is dedicated for snapshots
 *
 * @package Core\Entity
 */
interface SnapshotInterface
{
    public function __construct(EntityInterface $source);

    /**
     * Get the meta data.
     *
     * @return self|SnapshotMeta
     */
    public function getSnapshotMeta();

    public function getOriginalEntity();
}
