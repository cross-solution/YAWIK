<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Service;

use Core\Entity\EntityInterface;
use Core\Entity\Hydrator\CloneHydrator;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\SnapshotInterface;
use Core\Entity\SnapshotMeta;

/**
 * Class SnapshotGenerator
 * @package Core\Service
 */
class SnapshotGenerator
{

    /**
     * @var
     */
    protected $hydrator;

    /**
     *
     *
     * @var array
     */
    protected $snapshotAttributes = [];


    /**
     * @param $hydrator
     * @return $this
     */
    public function setHydrator($hydrator)
    {
        if ($hydrator instanceof EntityHydrator) {
            $this->hydrator = $hydrator;
        }
        return $this;
    }

    /**
     * @return EntityHydrator
     */
    public function getHydrator()
    {
        if (!isset($this->hydrator)) {
            $this->hydrator = new CloneHydrator();
        }
        return $this->hydrator;
    }

    /**
     * @param array $snapshotAttributes
     *
     * @return self
     */
    public function setSnapshotAttributes($snapshotAttributes)
    {
        $this->snapshotAttributes = $snapshotAttributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getSnapshotAttributes()
    {
        return $this->snapshotAttributes;
    }



    public function __invoke($source, $attributes = [], $target = null)
    {
        if (!is_array($attributes)) {
            $target = $attributes;
            $attributes = null;
        }

        if (!$target) {
            $target = get_class($source) . 'Snapshot';
            $target = new $target($source);
        }

        if ($target instanceof SnapshotAttributesProviderInterface) {
            $attributes = $target->getSnapshotAttributes();
        } elseif (empty($attributes)) {
            $attributes = $this->getSnapshotAttributes();
        }

        $hydrator = $this->getHydrator();
        $data     = $hydrator->extract($source, $attributes);
        $snapshot = $hydrator->hydrate($target, $data);

        return $snapshot;
    }
}
