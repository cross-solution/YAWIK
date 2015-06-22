<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Entity;

/**
 * Model interface
 */
interface SnapshotGeneratorProviderInterface
{

    /**
     * @return mixed
     */
    public function getSnapshotGenerator();

}