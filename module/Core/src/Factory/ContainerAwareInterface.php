<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

namespace Core\Factory;

use Interop\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * @param ContainerInterface $container
     *
     * @return mixed
     */
    public function setContainer(ContainerInterface $container);
}
