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

use Core\Entity\Hydrator\EntityHydrator;

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
     * @var
     */
    protected $source;

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
            $this->hydrator = new EntityHydrator();
        }
        return $this->hydrator;
    }

    /**
     * @param $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return array
     */
    public function getSnapshot()
    {
        $hydrator = $this->getHydrator();
        $source = $this->source;
        $data = $hydrator->extract($source);
        return $data;
    }
}
