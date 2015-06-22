<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Service;

use Core\Entity\Hydrator\EntityHydrator;


class SnapshotGenerator {

    protected $hydrator;

    protected $source;

    public function setHydrator($hydrator)
    {
        if ($hydrator instanceof EntityHydrator) {
            $this->hydrator = $hydrator;
        }
        return $this;
    }

    public function getHydrator()
    {
        if (!isset($this->hydrator)) {
            $this->hydrator = new EntityHydrator();
        }
        return $this->hydrator;
    }


    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getSnapshot()
    {
        $hydrator = $this->getHydrator();
        $source = $this->source;
        $data = $hydrator->extract($source);
        return $data;
    }

} 