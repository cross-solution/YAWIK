<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Entity;

use Core\Entity\Collection\ArrayCollection;
use Core\Exception\ImmutablePropertyException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
trait SnapshotTrait
{
    /**
     *
     * @ODM\EmbedOne(discriminatorField="_entity")
     * @var SnapshotMeta
     */
    protected $snapshotMeta;

    public function __construct(EntityInterface $source)
    {
        $snapshotMetaClass = defined('static::SNAPSHOTMETA_ENTITY_CLASS')
            ? static::SNAPSHOTMETA_ENTITY_CLASS
            : SnapshotMeta::class;

        $this->snapshotMeta = new $snapshotMetaClass();
        $this->snapshotEntity       = $source;
    }

    public function getOriginalEntity()
    {
        return $this->snapshotEntity;
    }

    public function getSnapshotMeta()
    {
        return $this->snapshotMeta;
    }

    public function getSnapshotAttributes()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return property_exists($this, 'snapshotAttributes') ? $this->snapshotAttributes : [];
    }

    /**
     *
     *
     * @param string $method
     * @param mixed[]  ...$args Arguments to be passed to proxied method.
     *
     * @return SnapshotTrait
     */
    protected function proxy($method, ...$args)
    {
        $entity   = $this->getOriginalEntity();
        $callback = [$entity, $method];

        if (!is_callable($callback)) {
            throw new \BadMethodCallException(sprintf(
                'Proxy error: Method "%s" does not exist in proxied "%s"',
                 $method, get_class($entity)
            ));
        }

        $return = $callback(...$args);

        return $return === $entity ? $this : $return;
    }

    protected function proxyClone($method, ...$args)
    {
        $value = $this->proxy($method, ...$args);
        $return = is_object($value) ? clone $value : $value;

        return $return;
    }

    protected function inaccessible($property)
    {
        throw new \DomainException(sprintf(
            'Property "%s" of "%s" must not be accessed directly. Please retrieve from original entity via getSnaphotMeta()->getEntity()',
            $property, get_class($this)
        ));
    }

    protected function immutable($property)
    {
        throw new ImmutablePropertyException($property, $this);
    }
}
