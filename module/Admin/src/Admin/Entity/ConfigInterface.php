<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @author cbleek
 * @license   AGPLv3
 */

namespace Admin\Entity;

use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\PermissionsAwareInterface;
use Core\Entity\ModificationDateAwareEntityInterface;
use Zend\Stdlib\Hydrator\HydratorAwareInterface;

/**
 * Interface ConfigInterface
 * @package Admin\Entity
 */
interface ConfigInterface
    extends EntityInterface,
    IdentifiableEntityInterface,

    ModificationDateAwareEntityInterface,
    PermissionsAwareInterface,
    HydratorAwareInterface
{

    /**
     * Sets the name of the organization
     *
     * @param string $name
     * @return ConfigInterface
     */
    public function setName($name);

    /**
     * Gets the name of the configuration parameter
     *
     * @return string $name
     */
    public function getName();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return ConfigInterface
     */
    public function setValue($value);

}