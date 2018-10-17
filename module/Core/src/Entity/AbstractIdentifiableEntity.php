<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core Entitys */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Base class for an Entity that should have id on it's class
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @ODM\MappedSuperclass
  */
abstract class AbstractIdentifiableEntity extends AbstractEntity implements IdentifiableEntityInterface
{
    use IdentifiableEntityTrait;
}
