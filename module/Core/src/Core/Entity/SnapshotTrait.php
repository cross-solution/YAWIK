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
     * @ODM\EmbedOne(targetDocument="Core\Entity\SnapshotMeta")
     * @var SnapshotMeta
     */
    protected $snapshotMeta;

    public function __construct(EntityInterface $source)
    {
        $this->snapshotMeta = new SnapshotMeta($source);
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

    protected function proxy()
    {
        /* @var SnapshotMeta $meta */
        $args     = func_get_args();
        $method   = array_shift($args);
        $meta     = $this->getSnapshotMeta();
        $entity   = $meta->getEntity();
        $callback = [$entity, $method];

        if (!is_callable($callback)) {
            throw new \BadMethodCallException(sprintf(
                'Proxy error: Method "%s" does not exist in proxied "%s"',
                 $method, get_class($entity)
            ));
        }

        $return = call_user_func_array($callback, $args);

        return $return === $entity ? $this : $return;
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